<?php

namespace App\Controller\Api;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestMercureController
{
    #[Route('/mercure-debug', name: 'mercure_debug')]
    public function debug(): Response
    {
        $jwt = $this->generateMercureJwt();

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => 'http:/mercure/.well-known/mercure',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'topic' => 'https://chat.localhost/messages/test',
                'data' => json_encode(['message' => 'debug test'])
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $jwt,
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new Response(
            "✅ Mercure Debug\n\n".
            "HTTP status: $status\n\n".
            "Response: $response\n\n".
            "cURL Error: $error\n\n".
            "JWT: $jwt"
        );
    }

    private function generateMercureJwt(): string
    {
        return JWT::encode(
            [
                'mercure' => [
                    'publish' => ['*'],
                    'subscribe' => ['*'],
                ],
                'exp' => time() + 3600
            ],
            'esgi-pa-super-secret-cle-mercure-2025', // ⚠️ Même que dans MERCURE_JWT_SECRET
            'HS256'
        );
    }
}