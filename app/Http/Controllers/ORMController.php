<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Seller;
use App\Models\Phone;
use App\Models\Category;
use App\Models\Publication;
use App\Models\Comment;
use App\Models\ReasonComplaint;
use App\Models\Complaint;
use App\Models\ChatSupport;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class ORMController extends Controller
{
    /**
     * Mostrar Role con sus relaciones
     */
    public function showRole(Role $role): JsonResponse
    {
        $role->load(['users']);
        
        return response()->json([
            'role' => $role
        ]);
    }

    /**
     * Mostrar User con sus relaciones
     */
    public function showUser(User $user): JsonResponse
    {
        $user->load([
            'role',
            'seller',
            'comments',
            'complaints',
            'favoritePublications',
        ]);
        
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Mostrar Seller con sus relaciones
     */
    public function showSeller(Seller $seller): JsonResponse
    {
        $seller->load([
            'user',
            'phones',
            'publications'
        ]);
        
        return response()->json([
            'seller' => $seller
        ]);
    }

    /**
     * Mostrar Phone con sus relaciones
     */
    public function showPhone(Phone $phone): JsonResponse
    {
        $phone->load(['seller']);
        
        return response()->json([
            'phone' => $phone
        ]);
    }

    /**
     * Mostrar Category con sus relaciones
     */
    public function showCategory(Category $category): JsonResponse
    {
        $category->load(['publications']);
        
        return response()->json([
            'category' => $category
        ]);
    }

    /**
     * Mostrar Publication con sus relaciones
     */
    public function showPublication(Publication $publication): JsonResponse
    {
        $publication->load([
            'seller',
            'category',
            'comments',
            'complaints',
            'usersWhoFavorited',
        ]);
        
        return response()->json([
            'publication' => $publication
        ]);
    }

    /**
     * Mostrar Comment con sus relaciones
     */
    public function showComment(Comment $comment): JsonResponse
    {
        $comment->load([
            'user',
            'publication'
        ]);
        
        return response()->json([
            'comment' => $comment
        ]);
    }

    /**
     * Mostrar ReasonComplaint con sus relaciones
     */
    public function showReasonComplaint(ReasonComplaint $reasonComplaint): JsonResponse
    {
        $reasonComplaint->load(['complaints']);
        
        return response()->json([
            'reasonComplaint' => $reasonComplaint
        ]);
    }

    /**
     * Mostrar Complaint con sus relaciones
     */
    public function showComplaint(Complaint $complaint): JsonResponse
    {
        $complaint->load([
            'user',
            'publication',
            'reasonComplaint'
        ]);
        
        return response()->json([
            'complaint' => $complaint
        ]);
    }

    /**
     * Mostrar ChatSupport con sus relaciones
     */
    /* public function showChatSupport(ChatSupport $chatSupport): JsonResponse
    {
        $chatSupport->load(['user']);
        
        return response()->json([
            'chatSupport' => $chatSupport
        ]);
    } */

    /**
     * Mostrar Chat con sus relaciones
     */
    /* public function showChat(Chat $chat): JsonResponse
    {
        $chat->load([
            'user',
            'publication',
            'messages'
        ]);
        
        return response()->json([
            'chat' => $chat
        ]);
    } */

    /**
     * Mostrar Message con sus relaciones
     */
    /* public function showMessage(Message $message): JsonResponse
    {
        $message->load([
            'chat',
            'sender'
        ]);
        
        return response()->json([
            'message' => $message
        ]);
    } */

    /**
     * Método principal que prueba todas las relaciones con UNA SOLA RUTA
     */
    public function testAllRelations(Request $request): JsonResponse
    {
        $results = [];

        // Role - primer registro
        $role = Role::first();
        if ($role) {
            $role->load(['users']);
            $results['role'] = $role;
        }

        // User - primer registro
        $user = User::first();
        if ($user) {
            $user->load([
                'role',
                'seller',
                'comments',
                'complaints',
                'favoritePublications',
            ]);
            $results['user'] = $user;
        }

        // Seller - primer registro
        $seller = Seller::first();
        if ($seller) {
            $seller->load([
                'user',
                'phones',
                'publications'
            ]);
            $results['seller'] = $seller;
        }

        // Phone - primer registro
        $phone = Phone::first();
        if ($phone) {
            $phone->load(['seller']);
            $results['phone'] = $phone;
        }

        // Category - primer registro
        $category = Category::first();
        if ($category) {
            $category->load(['publications']);
            $results['category'] = $category;
        }

        // Publication - primer registro
        $publication = Publication::first();
        if ($publication) {
            $publication->load([
                'seller',
                'category',
                'comments',
                'complaints',
                'usersWhoFavorited',
            ]);
            $results['publication'] = $publication;
        }

        // Comment - primer registro
        $comment = Comment::first();
        if ($comment) {
            $comment->load([
                'user',
                'publication'
            ]);
            $results['comment'] = $comment;
        }

        // ReasonComplaint - primer registro
        $reasonComplaint = ReasonComplaint::first();
        if ($reasonComplaint) {
            $reasonComplaint->load(['complaints']);
            $results['reasonComplaint'] = $reasonComplaint;
        }

        // Complaint - primer registro
        $complaint = Complaint::first();
        if ($complaint) {
            $complaint->load([
                'user',
                'publication',
                'reasonComplaint'
            ]);
            $results['complaint'] = $complaint;
        }

        // ChatSupport - primer registro
        /* $chatSupport = ChatSupport::first();
        if ($chatSupport) {
            $chatSupport->load(['user']);
            $results['chatSupport'] = $chatSupport;
        } */

        // Chat - primer registro
        /* $chat = Chat::first();
        if ($chat) {
            $chat->load([
                'user',
                'publication',
                'messages'
            ]);
            $results['chat'] = $chat;
        } */

        // Message - primer registro
        /* $message = Message::first();
        if ($message) {
            $message->load([
                'chat',
                'sender'
            ]);
            $results['message'] = $message;
        } */

        return response()->json([
            'message' => 'Todas las relaciones probadas exitosamente',
            'total_models_tested' => count($results),
            'data' => $results
        ]);
    }
}

// =====================================================================
// RUTAS PARA AGREGAR EN routes/api.php
// =====================================================================

/*
Route::prefix('orm-test')->group(function () {
    Route::get('/roles/{role}', [ORMController::class, 'showRole']);
    Route::get('/users/{user}', [ORMController::class, 'showUser']);
    Route::get('/sellers/{seller}', [ORMController::class, 'showSeller']);
    Route::get('/phones/{phone}', [ORMController::class, 'showPhone']);
    Route::get('/categories/{category}', [ORMController::class, 'showCategory']);
    Route::get('/publications/{publication}', [ORMController::class, 'showPublication']);
    Route::get('/comments/{comment}', [ORMController::class, 'showComment']);
    Route::get('/reason-complaints/{reasonComplaint}', [ORMController::class, 'showReasonComplaint']);
    Route::get('/complaints/{complaint}', [ORMController::class, 'showComplaint']);
    Route::get('/chat-supports/{chatSupport}', [ORMController::class, 'showChatSupport']);
    Route::get('/chats/{chat}', [ORMController::class, 'showChat']);
    Route::get('/messages/{message}', [ORMController::class, 'showMessage']);
});
*/