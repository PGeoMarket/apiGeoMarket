<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    protected $fcmUrl;
    protected $projectId;
    protected $serviceAccount;

    public function __construct()
    {
        $this->projectId = 'geomarket-9e06d';
        $this->fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        
        // 🔥 CREDENCIALES DIRECTAS EN EL CÓDIGO
        $this->serviceAccount = [
            "type" => "service_account",
            "project_id" => "geomarket-9e06d",
            "private_key_id" => "8768451f799cc7d297e16361313397ff281e1f3d",
            "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDEC6krXp+zoE2j\nJ8OBYudlnL4BoJzDdQS1yaY2b8t7D/mseWIjYywTiauyPhg0e149BbSHtWtTa8wN\nvp/0xDRHji+tEhE16Js9KeK1rwFw5Jc+P3Q4/YP4gCgbYNVzyXSVU0S/3CdiyGLQ\nDN1gcbRbe3jwcC3GXMpfZvvo+1/jasrN2d2NKwJz03WEvybBtP8HTpAOPfrdIdAG\nbhNYex4xzglWIH0w90kS0iOwNyHGsq28Y5/ceDBdjL0vA1UAuqHXs687z80YNv9R\nV+sOr2hfcKq7HY78FaBHlgVbwwpRtvmpDJIQoObSfNWttz9KNC/xwGkMmZlMFccc\n1KktjIjRAgMBAAECggEAAsuVhLi5e1W+G6Uez5DH4roTl7l4+Ly5taeXlQu+pY1c\nkovrxTxGEex+6CiEXvWyGhnDaWKx4j9tijXhSRu10N4fMgcZm5iao4puCQgC88+P\nlD+yfhhHxh6aK8tDed5ZySIF/zwR6/G2XnfrfWM7pokF+DwqO+uBZ280CO4yH5+F\nFx5qnZUPTrj+WI/QosRYOh8VUD4ZJvjoO1qSxYglRULVYrqtvhtiovCn9aZfDHn8\nOHKc499kNoa4EeTbz3KGySRgvkWGsDw+2eiTbMi1dziYmiH95B/P2qmPTHClsq8+\nS6Bz/7zYLlCGTasFhqVmLx3k0BrUSgfOYohUdbXdgQKBgQDxXNXn1/38uaGMZruh\nxqxdmJBzbAREXbo5Vw67b6nxYUwogN4zrSDBomSN76UcDxSngItpxLj7tTpXl7gD\nI2qldshrER+Pyg510ve+6RIUNylDLMHpsaRn4QiojE2GaBVOVeBDMZmCMz9IGdeV\nJjPjdMY+C0ck5ac6f+epttEMUQKBgQDP70aSLKTBtRzZIG9wwOQGLJTdPf8usjVP\n5a/03FK6OKt677Z9JaDv5o/13PjFooNPfEnQFI2uSOYsO8siqMYNAoLz2uRNmlZ4\nbn0e+9midghoVRdn0rd/VFcYN6FyxII4c2GPJgcf0GlfKCbo86Z8ZJLESX04xCtH\n12i6WI8UgQKBgEJ8hTwBRqjIZdTsM8GDndWGgjwZRC+k9fh3n8pIHzMrzzPVE+B+\nT2inmDV1DzFkghcGFOFE3IQRzwlz9K+AoQ8FYn4D0ILmcQdJ3w8K2v0QmOA1QxFh\n6tzmo2DyjSR6JWxXwZgg4J16CnONEtK2HFMKxtUufCGQ1XkK5MDeaEWRAoGAInGY\nVw5eHFhL9wuQajUJkJxB7IQgiTOr8RgzFXSJn59TiIG80O4ywoqGvktkShipd7k2\n4OkGryAUQK+G7q7WX8FSv+I6f0BZoolq4H8HhgnXSrENt30IOGdYJgLRE5nJmGBE\ngNnjxDlZuxGDoIL7yQ8/4JPr0kNsh/H+vx98VAECgYAjbAeLWEn9u5S6z3DPQ4at\n10FVTjxK36ejBn+Sywt9dif1PLdo3LBOtaTY68ymucJ8+YEC/DtWQ+i2psoehq1+\nb/OlcQUf2LTbmWzGJpH7UEdGeHEq1beJX0zQ719krAwOkItKxf8rLxGaIWhfBxyK\nfnNAWa7BZAF0FHRPvnGHyQ==\n-----END PRIVATE KEY-----\n",
            "client_email" => "firebase-adminsdk-fbsvc@geomarket-9e06d.iam.gserviceaccount.com",
            "client_id" => "116362592394546294446",
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40geomarket-9e06d.iam.gserviceaccount.com",
            "universe_domain" => "googleapis.com"
        ];
    }

    /**
     * Generar access token usando JWT
     */
    public function getAccessToken()
    {
        try {
            $serviceAccount = $this->serviceAccount;

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
            openssl_sign($dataToSign, $signature, $privateKey, 'SHA256');
            $signatureEncoded = $this->base64UrlEncode($signature);
            
            $jwt = $dataToSign . '.' . $signatureEncoded;

            // Intercambiar JWT por access token
            $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            $tokenData = $response->json();

            if ($response->successful() && isset($tokenData['access_token'])) {
                return $tokenData['access_token'];
            } else {
                throw new \Exception("Error obteniendo access token: " . json_encode($tokenData));
            }

        } catch (\Exception $e) {
            throw new \Exception("Error en getAccessToken: " . $e->getMessage());
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
     * Enviar notificación a un token específico
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
                    'data' => $data,
                    'android' => [
                        'priority' => 'high'
                    ]
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
    /**
 * Limpiar tokens inválidos/expirados
 */
public function cleanInvalidTokens($userId = null)
{
    try {
        $query = DeviceToken::where('is_active', true);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $tokens = $query->get();
        $totalTokens = $tokens->count();
        $invalidCount = 0;

        foreach ($tokens as $token) {
            // Probar cada token individualmente
            $isValid = $this->validateToken($token->fcm_token);
            
            if (!$isValid) {
                $token->update(['is_active' => false]);
                $invalidCount++;
            }
        }

        return [
            'total_tokens' => $totalTokens,
            'invalid_tokens' => $invalidCount,
            'valid_tokens' => $totalTokens - $invalidCount
        ];

    } catch (\Exception $e) {
        throw new \Exception("Error limpiando tokens: " . $e->getMessage());
    }
}

/**
 * Validar si un token FCM es válido
 */
private function validateToken($token)
{
    try {
        $accessToken = $this->getAccessToken();
        
        $payload = [
            'message' => [
                'token' => $token,
                'data' => ['test' => 'validation'],
                'android' => ['priority' => 'high']
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->timeout(10)->post($this->fcmUrl, $payload);

        $result = $response->json();

        // Si recibe UNREGISTERED, el token es inválido
        if (isset($result['error']['details'][0]['errorCode']) && 
            $result['error']['details'][0]['errorCode'] === 'UNREGISTERED') {
            return false;
        }

        return $response->successful();

    } catch (\Exception $e) {
        return false;
    }
}
}