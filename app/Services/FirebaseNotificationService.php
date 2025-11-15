<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $fcmUrl;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID', 'geomarket-9e06d');
        $this->fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Generar access token usando JWT
     */
    private function getAccessToken()
    {
        try {
            $serviceAccountPath = env('GOOGLE_APPLICATION_CREDENTIALS');
            
            if (!file_exists($serviceAccountPath)) {
                Log::error("❌ Archivo de credenciales no encontrado: " . $serviceAccountPath);
                return null;
            }

            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            
            $now = time();
            $payload = [
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now
            ];

            // Headers para JWT
            $header = [
                'alg' => 'RS256',
                'typ' => 'JWT'
            ];

            // Codificar header y payload
            $headerEncoded = $this->base64UrlEncode(json_encode($header));
            $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
            
            $dataToSign = $headerEncoded . '.' . $payloadEncoded;
            
            // Firmar con la clave privada
            $privateKey = $serviceAccount['private_key'];
            openssl_sign($dataToSign, $signature, $privateKey, 'SHA256');
            $signatureEncoded = $this->base64UrlEncode($signature);
            
            $jwt = $dataToSign . '.' . $signatureEncoded;

            // Intercambiar JWT por access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            $tokenData = $response->json();
            
            if (isset($tokenData['access_token'])) {
                Log::info("✅ Access token generado correctamente");
                return $tokenData['access_token'];
            } else {
                Log::error("❌ Error obteniendo access token: " . json_encode($tokenData));
                return null;
            }

        } catch (\Exception $e) {
            Log::error("❌ Error generando access token: " . $e->getMessage());
            return null;
        }
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Enviar notificación a un usuario
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        try {
            Log::info("🔔 Intentando enviar notificación a usuario: $userId - Titulo: '$title'");

            // Obtener tokens del usuario
            $tokens = DeviceToken::where('user_id', $userId)
                ->where('is_active', true)
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                Log::info("❌ Usuario $userId no tiene tokens FCM activos");
                return false;
            }

            Log::info("📱 Tokens encontrados para usuario $userId: " . count($tokens));

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error("❌ No se pudo obtener access token");
                return false;
            }

            $successCount = 0;
            foreach ($tokens as $token) {
                if ($this->sendToToken($token, $title, $body, $data, $accessToken)) {
                    $successCount++;
                }
            }

            Log::info("✅ Notificaciones enviadas a usuario $userId: $successCount éxitos de " . count($tokens));
            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error("❌ Error enviando notificación FCM: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación a un token específico
     */
    public function sendToToken($token, $title, $body, $data = [], $accessToken = null)
    {
        try {
            if (!$accessToken) {
                $accessToken = $this->getAccessToken();
            }

            if (!$accessToken) {
                Log::error("❌ No hay access token disponible");
                return false;
            }

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'data' => $data,
                    'android' => [
                        'priority' => 'high'
                    ]
                ]
            ];

            Log::info("📤 Enviando payload FCM v1: " . json_encode($payload));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($this->fcmUrl, $payload);

            $result = $response->json();
            $statusCode = $response->status();

            Log::info("📥 Respuesta FCM - Status: $statusCode, Response: " . json_encode($result));

            if ($response->successful()) {
                Log::info("✅ Notificación FCM v1 enviada correctamente");
                return true;
            } else {
                Log::error("❌ Error FCM v1 - Status: $statusCode, Error: " . json_encode($result));
                return false;
            }

        } catch (\Exception $e) {
            Log::error("❌ Excepción enviando notificación FCM v1: " . $e->getMessage());
            return false;
        }
    }
}