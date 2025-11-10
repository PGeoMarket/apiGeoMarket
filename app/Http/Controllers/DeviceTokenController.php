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
}