<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CoordinateController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\ORMController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ReasonComplaintController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupportController;
use App\Models\Coordinate;
use App\Models\DeviceToken;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('comments', CommentController::class);
Route::apiResource('phones', PhoneController::class);
Route::apiResource('publications', PublicationController::class);
Route::apiResource('reasonComplaints', ReasonComplaintController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('sellers', SellerController::class);
Route::apiResource('reports', ReportController::class);
Route::apiResource('coordinates', CoordinateController::class);


Route::apiResource('images', ImageController::class);

//rutas del usuario y favoritos
Route::apiResource('users', UserController::class);
Route::get('users/{id}/favorites', [UserController::class, 'favoritos']);
Route::patch('users/{userId}/favorites/toggle', [UserController::class, 'toggleFavorito']);


Route::get('orm/test-all', [ORMController::class, 'testAllRelations']);
Route::get('orm/test-polymorphic', [ORMController::class, 'testPolymorphicRelations']);


Route::post('/support', [SupportController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::post('publications/{publication}/report', [ReportController::class, 'reportPublication']);
Route::post('users/{user}/report', [ReportController::class, 'reportUser']);

Route::post('users/{user}/suspend/temporary', [UserController::class, 'suspendTemporarily']);
Route::post('users/{user}/suspend/permanent', [UserController::class, 'suspendPermanently']);
Route::post('users/{user}/unsuspend', [UserController::class, 'unsuspend']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    // Crear chat desde publicación
    Route::post('chats/from-publication', [ChatController::class, 'createFromPublication']);
    
    // Obtener mis chats
    Route::get('chats', [ChatController::class, 'getMyChats']);
    
    // Obtener mensajes de un chat
    Route::get('chats/{chatId}/messages', [ChatController::class, 'getChatMessages']);
    
    // Enviar mensaje
    Route::post('chats/{chatId}/messages', [ChatController::class, 'sendMessage']);
    
    // Cerrar chat
    Route::patch('chats/{chatId}/close', [ChatController::class, 'closeChat']);

    Route::post('/device-token', [DeviceTokenController::class, 'store']);
    Route::delete('/device-token', [DeviceTokenController::class, 'destroy']);
});

Route::get('/clean-and-test-tokens', function () {
    $debugInfo = [];
    $userId = 4;
    
    $debugInfo[] = "🧹 INICIANDO LIMPIEZA DE TOKENS INVÁLIDOS";
    $debugInfo[] = "Usuario ID: " . $userId;
    
    try {
        $service = new App\Services\FirebaseNotificationService();
        
        // 1. Limpiar tokens inválidos
        $debugInfo[] = "🔍 Buscando tokens inválidos...";
        $cleanupResult = $service->cleanInvalidTokens($userId);
        
        $debugInfo[] = "📊 Resultado limpieza:";
        $debugInfo[] = "   - Total tokens: " . $cleanupResult['total_tokens'];
        $debugInfo[] = "   - Tokens inválidos: " . $cleanupResult['invalid_tokens'];
        $debugInfo[] = "   - Tokens válidos: " . $cleanupResult['valid_tokens'];
        
        // 2. Si hay tokens válidos, probar notificación
        if ($cleanupResult['valid_tokens'] > 0) {
            $debugInfo[] = "🚀 Probando notificación con tokens válidos...";
            
            $result = $service->sendToUser(
                $userId,
                '🔔 Prueba Después de Limpieza',
                'Notificación después de limpiar tokens inválidos',
                ['test' => 'after_cleanup', 'timestamp' => now()->toISOString()]
            );
            
            $debugInfo[] = "📦 Resultado notificación: " . ($result ? 'ÉXITO 🎉' : 'FALLO ❌');
            
        } else {
            $debugInfo[] = "⚠️ No hay tokens válidos después de la limpieza";
            $debugInfo[] = "💡 El usuario necesita abrir la app para generar nuevos tokens";
            $result = false;
        }
        
        return response()->json([
            'success' => $result,
            'debug_info' => $debugInfo,
            'cleanup_result' => $cleanupResult,
            'user_id' => $userId,
            'next_steps' => $cleanupResult['valid_tokens'] > 0 ? 
                'Los tokens válidos funcionaron correctamente' : 
                'El usuario debe abrir la app para generar nuevos tokens FCM'
        ]);
        
    } catch (Exception $e) {
        $debugInfo[] = "💥 EXCEPCIÓN: " . $e->getMessage();
        
        return response()->json([
            'success' => false,
            'debug_info' => $debugInfo,
            'error' => $e->getMessage()
        ]);
    }
});


Route::get('/test-noti-simple', function () {
    $service = new FirebaseNotificationService();
    
    // Usuario 4 (como en tu prueba)
    $result = $service->sendNotification(
        4,
        '🔥 NOTIFICACIÓN DE PRUEBA',
        '¡Esta debería funcionar!',
        ['test' => 'simple', 'time' => now()->toISOString()]
    );
    
    return response()->json($result);
});