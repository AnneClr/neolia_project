<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Api\User\UserEntity;

class AuthService {
    private $secretKey = 'your_secret_key';

    public function generateToken(UserEntity $user): string {
        $payload = [
            'iss' => 'your-domain.com', // Issuer
            'iat' => time(), // Issued at
            'exp' => time() + (60 * 60), // Expiration time (1 hour)
            'sub' => $user->getId(), // Subject (user ID)
            'name' => $user->getLogin() // Additional user data
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token) {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
