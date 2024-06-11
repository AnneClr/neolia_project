<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Aelion\Http\Request\Request;
use Aelion\Http\Request\Exception\UnauthorizedException;
use Closure;
use App\Services\AuthService;

class AuthMiddleware {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function handle(Request $request, Closure $next) {
        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader) {
            throw new UnauthorizedException('Authorization header not found');
        }

        list($jwt) = sscanf($authHeader, 'Bearer %s');

        if (!$jwt) {
            throw new UnauthorizedException('Invalid authorization header format');
        }

        $decoded = $this->authService->validateToken($jwt);
        if (!$decoded) {
            throw new UnauthorizedException('Invalid or expired token');
        }

        $request->set('user', (string) $decoded);
        return $next($request);
    }
}
