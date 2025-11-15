<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;

class FirebaseNotificationService
{
    private function getAccessToken()
    {
        $serviceAccount = [
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

        openssl_sign($dataToSign, $signature, $serviceAccount['private_key'], 'SHA256');
        $signatureEncoded = $this->base64UrlEncode($signature);
        $jwt = $dataToSign . '.' . $signatureEncoded;

        $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        $tokenData = $response->json();
        return $tokenData['access_token'] ?? null;
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function sendChatNotification($recipientUserId, $senderName, $messageText, $chatId, $senderId)
    {
        try {
            // 1. Obtener tokens activos
            $tokens = DeviceToken::where('user_id', $recipientUserId)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($tokens->isEmpty()) {
                error_log("Usuario $recipientUserId sin tokens activos");
                return false;
            }

            error_log("Enviando notificación a usuario $recipientUserId - Tokens: " . $tokens->count());

            // 2. Obtener access token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                error_log("No se pudo generar access token");
                return false;
            }

            // 3. Preparar notificación
            $title = "💬 $senderName";
            $body = strlen($messageText) > 100 ? substr($messageText, 0, 100) . '...' : $messageText;

            $data = [
                'type' => 'chat_message',
                'chat_id' => (string)$chatId,
                'sender_id' => (string)$senderId,
                'sender_name' => $senderName,
                'action' => 'open_chat'
            ];

            // 4. Enviar a cada token
            $successCount = 0;
            $invalidTokens = [];

            foreach ($tokens as $token) {
                $result = $this->sendToToken($token->fcm_token, $title, $body, $data, $accessToken);
                
                if ($result['success']) {
                    $successCount++;
                    error_log("Notificación enviada exitosamente");
                    break; // Con uno que funcione es suficiente
                } else if ($result['invalid_token']) {
                    $invalidTokens[] = $token->id;
                }
            }

            // 5. Limpiar tokens inválidos
            if (!empty($invalidTokens)) {
                DeviceToken::whereIn('id', $invalidTokens)->update(['is_active' => false]);
                error_log("Desactivados " . count($invalidTokens) . " tokens inválidos");
            }

            return $successCount > 0;

        } catch (\Exception $e) {
            error_log("Error en sendChatNotification: " . $e->getMessage());
            return false;
        }
    }

    private function sendToToken($fcmToken, $title, $body, $data, $accessToken)
    {
        try {
            $fcmUrl = "https://fcm.googleapis.com/v1/projects/geomarket-9e06d/messages:send";
            
            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'chat_messages',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($fcmUrl, $payload);

            if ($response->successful()) {
                return ['success' => true, 'invalid_token' => false];
            } else {
                $result = $response->json();
                $isInvalid = isset($result['error']['details'][0]['errorCode']) &&
                    $result['error']['details'][0]['errorCode'] === 'UNREGISTERED';
                
                error_log("Error FCM: " . json_encode($result));
                return ['success' => false, 'invalid_token' => $isInvalid];
            }

        } catch (\Exception $e) {
            error_log("Error enviando a token: " . $e->getMessage());
            return ['success' => false, 'invalid_token' => false];
        }
    }
}