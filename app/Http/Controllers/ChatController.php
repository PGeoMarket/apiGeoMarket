<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ably\AblyRest;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;



class ChatController extends Controller
{
    /**
     * Crear chat desde publicación
     */
    public function createFromPublication(Request $request)
    {
        $request->validate([
            'publication_id' => 'required|exists:publications,id'
        ]);
        

        try {
            // Obtener la publicación con el seller y user
            $publication = Publication::with('seller')->findOrFail($request->publication_id);
            
            $initiatorUserId = Auth::id();
            $responderUserId = $publication->seller->user_id; // user_id del seller

            // Validar que no se contacte a sí mismo
            if ($initiatorUserId == $responderUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes contactarte contigo mismo'
                ], 400);
            }

            // Crear o encontrar chat existente
            $chat = Chat::firstOrCreate([
                'initiator_user_id' => $initiatorUserId,
                'publication_id' => $request->publication_id
            ], [
                'responder_user_id' => $responderUserId
            ]);

            return response()->json([
                'success' => true,
                'chat' => $chat->load(['initiator', 'responder', 'publication']),
                'ably_channel' => $chat->ably_channel_id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el chat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener mis chats
     */
    public function getMyChats()
{
    try {
        $userId = Auth::id();
        
        $chats = Chat::forUser($userId)
            ->active()
            ->with([
                'initiator' => function ($query) {
                    $query->select('id', 'primer_nombre', 'primer_apellido')
                          ->with(['image:id,imageable_id,imageable_type,url']);
                },
                'responder' => function ($query) {
                    $query->select('id', 'primer_nombre', 'primer_apellido')
                          ->with(['image:id,imageable_id,imageable_type,url']);
                },
                'publication' => function ($query) {
                    $query->select('id', 'titulo')
                          ->with(['image:id,imageable_id,imageable_type,url']);
                },
                'latestMessage:id,chat_id,text,sent_at'
            ])
            ->orderByDesc('updated_at')
            ->get();

        // Agregar información del otro participante e imágenes
        $chats = $chats->map(function ($chat) use ($userId) {
            $otherParticipant = $chat->getOtherParticipant($userId);

            $chat->other_participant = $otherParticipant;
            $chat->other_participant_name = $otherParticipant->primer_nombre . ' ' . $otherParticipant->primer_apellido;

            // 🔹 Imagen del otro participante
            $chat->other_participant_image_url = $otherParticipant->image?->url ?? null;

            // 🔹 Imagen de la publicación
            $chat->publication_image_url = $chat->publication?->image?->url ?? null;

            return $chat;
        });

        return response()->json([
            'success' => true,
            'chats' => $chats
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los chats: ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * Obtener mensajes de un chat
     */
    public function getChatMessages($chatId)
    {
        try {
            $chat = Chat::findOrFail($chatId);
            
            // Verificar acceso
            if (!$chat->isParticipant(Auth::user()->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes autorización para ver este chat'
                ], 403);
            }

            $messages = $chat->messages()
                ->with('sender:id,primer_nombre,primer_apellido')
                ->get();
            
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'chat' => $chat->load(['publication', 'initiator', 'responder'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los mensajes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar mensaje (se guarda en BD)
     * El tiempo real lo maneja Ably desde Angular
     */
    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'text' => 'required|string|max:1000'
        ]);

        try {
            $chat = Chat::findOrFail($chatId);
            $userId = Auth::id();

            // Verificar acceso
            if (!$chat->isParticipant($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes autorización para escribir en este chat'
                ], 403);
            }

            // Crear mensaje
            $message = Message::create([
                'chat_id' => $chatId,
                'sender_id' => $userId,
                'text' => $request->text,
                'message_type' => $request->message_type ?? 'text'
            ]);

            // Cargar relación sender
            $message->load('sender:id,primer_nombre,primer_apellido');

            // Actualizar timestamp del chat
            $chat->touch();

            // 👇 NUEVO: Enviar push notification
            $this->sendPushNotification($chat, $message);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar un chat
     */
    public function closeChat($chatId)
    {
        try {
            $chat = Chat::findOrFail($chatId);
            
            // Solo los participantes pueden cerrar el chat
            if (!$chat->isParticipant(Auth::user()->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes autorización para cerrar este chat'
                ], 403);
            }

            $chat->update(['status' => 'closed']);

            return response()->json([
                'success' => true,
                'message' => 'Chat cerrado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar el chat: ' . $e->getMessage()
            ], 500);
        }
    }

   private function sendPushNotification(Chat $chat, Message $message)
{
    try {
        // 1. Determinar receptor
        $recipientId = $message->sender_id == $chat->initiator_user_id
            ? $chat->responder_user_id
            : $chat->initiator_user_id;

        $senderName = $message->sender->primer_nombre . ' ' . $message->sender->primer_apellido;

        // 2. Datos para la notificación
        $data = [
            'chat_id' => (string)$chat->id,
            'sender_id' => (string)$message->sender_id,
            'type' => 'chat_message'
        ];

        // 3. Enviar notificación (SOLUCIÓN DIRECTA)
        $firebaseService = new \App\Services\FirebaseNotificationService();
        $result = $firebaseService->sendNotification(
            $recipientId,
            $senderName,
            $message->text,
            $data
        );

        // 4. Log simple

    } catch (\Exception $e) {
    }
}

}