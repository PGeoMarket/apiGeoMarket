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

Route::get('/test-firebase-debug', function () {
    $debugInfo = [];
    $userId = 4; // El usuario que mencionaste
    
    $debugInfo[] = "🧪 INICIANDO PRUEBA DE FIREBASE DEBUG";
    $debugInfo[] = "Usuario ID: " . $userId;
    
    // 1. Verificar variables de entorno
    $debugInfo[] = "🔍 VARIABLES DE ENTORNO:";
    $debugInfo[] = "FIREBASE_PROJECT_ID: " . (env('FIREBASE_PROJECT_ID') ?: 'NO DEFINIDO');
    $credentialsJson = env('GOOGLE_APPLICATION_CREDENTIALS_JSON');
    $debugInfo[] = "GOOGLE_APPLICATION_CREDENTIALS_JSON: " . ($credentialsJson ? "DEFINIDA (" . strlen($credentialsJson) . " chars)" : 'NO DEFINIDA');
    
    if ($credentialsJson) {
        $debugInfo[] = "📄 PRIMEROS 50 CHARS: " . substr($credentialsJson, 0, 50) . "...";
        
        // Verificar JSON
        $jsonCheck = json_decode($credentialsJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $debugInfo[] = "❌ JSON INVÁLIDO: " . json_last_error_msg();
        } else {
            $debugInfo[] = "✅ JSON VÁLIDO";
            $debugInfo[] = "📧 Client Email: " . ($jsonCheck['client_email'] ?? 'NO ENCONTRADO');
            $debugInfo[] = "🔑 Private Key: " . (isset($jsonCheck['private_key']) ? 'PRESENTE' : 'FALTANTE');
        }
    }
    
    // 2. Verificar tokens del usuario
    $debugInfo[] = "🔍 VERIFICANDO TOKENS DEL USUARIO {$userId}:";
    $tokens = DeviceToken::where('user_id', $userId)
        ->where('is_active', true)
        ->get();
    
    $debugInfo[] = "Tokens encontrados: " . $tokens->count();
    
    foreach ($tokens as $index => $token) {
        $debugInfo[] = "Token " . ($index + 1) . ": " . substr($token->fcm_token, 0, 20) . "...";
        $debugInfo[] = "Plataforma: " . $token->platform . ", Activo: " . $token->is_active;
    }
    
    // 3. Probar el servicio Firebase
    $debugInfo[] = "🚀 PROBANDO SERVICIO FIREBASE:";
    
    try {
        $service = new FirebaseNotificationService();
        
        // Probar generación de token
        $debugInfo[] = "🔄 Generando access token...";
        $accessToken = $service->getAccessToken();
        
        if ($accessToken) {
            $debugInfo[] = "✅ Access token generado: " . substr($accessToken, 0, 20) . "...";
            
            // Enviar notificación real
            $debugInfo[] = "📤 Enviando notificación...";
            $result = $service->sendToUser(
                $userId,
                '🔔 Prueba Debug Railway',
                'Esta es una notificación de prueba con debug',
                ['debug' => 'true', 'test_id' => uniqid()]
            );
            
            $debugInfo[] = "📦 Resultado del envío: " . ($result ? 'ÉXITO' : 'FALLO');
            
        } else {
            $debugInfo[] = "❌ NO se pudo generar access token";
        }
        
    } catch (Exception $e) {
        $debugInfo[] = "💥 EXCEPCIÓN: " . $e->getMessage();
        $debugInfo[] = "📋 Trace: " . $e->getFile() . ":" . $e->getLine();
    }
    
    $debugInfo[] = "🎯 PRUEBA COMPLETADA";
    
    return response()->json([
        'success' => isset($result) ? $result : false,
        'debug_info' => $debugInfo,
        'user_id' => $userId,
        'tokens_count' => $tokens->count(),
        'timestamp' => now()->toISOString()
    ]);
});