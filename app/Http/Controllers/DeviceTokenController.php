<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DeviceTokenController extends Controller
{
    /**
     * Guardar token del dispositivo
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'fcm_token' => 'required|string',
            'platform' => 'required|in:android,ios'
        ]);

        $user =Auth::user();

        DeviceToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_id' => $request->device_id
            ],
            [
                'fcm_token' => $request->fcm_token,
                'platform' => $request->platform,
                'is_active' => true
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Token registrado'
        ]);
    }

    /**
     * Desactivar token (logout)
     */
    public function destroy(Request $request)
{
    // Leer device_id desde query params
    $deviceId = $request->query('device_id') ?? $request->input('device_id');
    
    if (!$deviceId) {
        return response()->json([
            'success' => false,
            'message' => 'device_id requerido'
        ], 400);
    }

    DeviceToken::where('user_id', auth::id())
        ->where('device_id', $deviceId)
        ->update(['is_active' => false]);

    return response()->json([
        'success' => true,
        'message' => 'Token desactivado'
    ]);
}
    public function cleanMyTokens()
    {
        try {
            $userId = Auth::id();
            $tokens = DeviceToken::where('user_id', $userId)
                ->where('is_active', true)
                ->get();

            $invalidCount = 0;

            foreach ($tokens as $token) {
                // Verificar si el token es válido
                $isValid = $this->validateToken($token->fcm_token);
                
                if (!$isValid) {
                    $token->update(['is_active' => false]);
                    $invalidCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Limpieza completada. $invalidCount tokens inválidos desactivados",
                'invalid_count' => $invalidCount,
                'remaining_tokens' => $tokens->count() - $invalidCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en limpieza: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar token individual con Firebase
     */
    private function validateToken($fcmToken)
    {
        // Implementación simple de validación
        // Puedes hacer una prueba de envío silenciosa aquí
        return true; // Por ahora asumimos que es válido
    }

    /**
     * Obtener estadísticas de mis tokens
     */
    public function getMyTokenStats()
    {
        $userId = Auth::id();
        
        $stats = [
            'total_tokens' => DeviceToken::where('user_id', $userId)->count(),
            'active_tokens' => DeviceToken::where('user_id', $userId)
                ->where('is_active', true)
                ->count(),
            'tokens_by_platform' => DeviceToken::where('user_id', $userId)
                ->where('is_active', true)
                ->selectRaw('platform, count(*) as count')
                ->groupBy('platform')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}