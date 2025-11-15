<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;

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
     * Generar access token usando JWT - VERSIÓN MEJORADA
     */
    public function getAccessToken()
    {
        try {
            $serviceAccountJson = env('GOOGLE_APPLICATION_CREDENTIALS_JSON');
            
            if (!$serviceAccountJson) {
                throw new \Exception("GOOGLE_APPLICATION_CREDENTIALS_JSON no está definida");
            }
            
            $serviceAccount = json_decode($serviceAccountJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON inválido: " . json_last_error_msg());
            }

            if (!isset($serviceAccount['private_key'])) {
                throw new \Exception("private_key no encontrada en el JSON");
            }

            $now = time();
            $payload = [
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now
            ];

            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $headerEncoded = $this->base64UrlEncode(json_encode($header));
            $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
            $dataToSign = $headerEncoded . '.' . $payloadEncoded;
            
            $privateKey = $serviceAccount['private_key'];
            
            // Verificar que la clave privada es válida
            if (!openssl_sign($dataToSign, $signature, $privateKey, 'SHA256')) {
                throw new \Exception("Error firmando JWT con openssl_sign");
            }
            
            $signatureEncoded = $this->base64UrlEncode($signature);
            $jwt = $dataToSign . '.' . $signatureEncoded;

            // Intercambiar JWT por access token
            $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if (!$response->successful()) {
                throw new \Exception("Error OAuth2: " . $response->body());
            }

            $tokenData = $response->json();
            
            if (!isset($tokenData['access_token'])) {
                throw new \Exception("Access token no encontrado en respuesta: " . json_encode($tokenData));
            }

            return $tokenData['access_token'];

        } catch (\Exception $e) {
            // No logueamos, lanzamos la excepción para que la capture la ruta de prueba
            throw new \Exception("Error en getAccessToken: " . $e->getMessage());
        }
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Enviar notificación a un usuario - VERSIÓN MEJORADA
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        try {
            // Obtener tokens del usuario
            $tokens = DeviceToken::where('user_id', $userId)
                ->where('is_active', true)
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                throw new \Exception("Usuario $userId no tiene tokens FCM activos");
            }

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new \Exception("No se pudo obtener access token");
            }

            $successCount = 0;
            foreach ($tokens as $token) {
                if ($this->sendToToken($token, $title, $body, $data, $accessToken)) {
                    $successCount++;
                }
            }

            return $successCount > 0;

        } catch (\Exception $e) {
            throw new \Exception("Error en sendToUser: " . $e->getMessage());
        }
    }

    /**
     * Enviar notificación a un token específico - VERSIÓN MEJORADA
     */
    public function sendToToken($token, $title, $body, $data = [], $accessToken = null)
    {
        try {
            if (!$accessToken) {
                $accessToken = $this->getAccessToken();
            }

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'data' => $data
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($this->fcmUrl, $payload);

            $result = $response->json();

            if ($response->successful()) {
                return true;
            } else {
                throw new \Exception("FCM Error - Status: " . $response->status() . ", Response: " . json_encode($result));
            }

        } catch (\Exception $e) {
            throw new \Exception("Error en sendToToken: " . $e->getMessage());
        }
    }
}