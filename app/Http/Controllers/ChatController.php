<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
            
            $initiatorUserId = 1;
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
            $userId = 1;
            
            $chats = Chat::forUser($userId)
                ->active()
                ->with([
                    'initiator:id,primer_nombre,primer_apellido',
                    'responder:id,primer_nombre,primer_apellido',
                    'publication:id,titulo',
                    'latestMessage:id,chat_id,content,sent_at'
                ])
                ->orderByDesc('updated_at')
                ->get();

            // Agregar información del otro participante para cada chat
            $chats = $chats->map(function ($chat) use ($userId) {
                $otherParticipant = $chat->getOtherParticipant($userId);
                $chat->other_participant = $otherParticipant;
                $chat->other_participant_name = $otherParticipant->primer_nombre . ' ' . $otherParticipant->primer_apellido;
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
            'content' => 'required|string|max:1000'
        ]);

        try {
            $chat = Chat::findOrFail($chatId);
            $userId = 1;

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

            // Actualizar timestamp del chat
            $chat->touch();

            return response()->json([
                'success' => true,
                'message' => $message->load('sender:id,primer_nombre,primer_apellido')
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
}