<?php
use Firebase\JWT\JWT;

// ...

function generateJWT($user) {
    $payload = [
        'iss' => 'your_issuer',
        'aud' => 'your_audience',
        'iat' => time(),
        'exp' => time() + (60*60), // token valid for 1 hour
        'data' => [
            'userId' => $user->getId()
        ]
    ];

    $jwt = JWT::encode($payload, 'your_secret_key', 'HS256');

    return $jwt;
}