<?php

/**
 * Router
 *  Map routes and load controller according to method and path
 * @author Aelion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - Extends AltoRouter
 *  - Maps Hello route
 */

namespace Aelion\Router;

use Api\User\UserRepository;
use Aelion\Http\Response\JsonResponse;
use Aelion\Dbal\Exception\NotFoundException;
use Aelion\Dbal\Exception\IncorrectSqlExpressionException;

class Router extends \AltoRouter
{
    public function __construct()
    {
        parent::__construct();
        $this->setMapping();
    }

    private function setMapping(): void
    {
        $this->map(
            'GET',
            '/',
            function () {
                JsonResponse::sendJson(200, ['message' => 'Welcome to the API']);
            }
        );

        $this->map(
            'POST',
            '/signin',
            function () {
                $requestData = json_decode(file_get_contents('php://input'), true);

                if (isset($requestData['username']) && isset($requestData['password'])) {
                    $userRepository = new UserRepository();

                    try {
                        // Authentifier l'utilisateur
                        $user = $userRepository->findByLoginAndPassword($requestData['username'], $requestData['password']);

                        // Générer le JWT (fonction à implémenter)
                        $jwt = generateJWT($user);

                        // Envoyer le JWT en tant que réponse
                        JsonResponse::sendJson(200, array("jwt" => $jwt));
                    } catch (NotFoundException $e) {
                        JsonResponse::sendJson(401, array("message" => "Invalid username or password"));
                    } catch (IncorrectSqlExpressionException $e) {
                        JsonResponse::sendJson(500, array("message" => "Internal server error"));
                    }
                } else {
                    JsonResponse::sendJson(400, array("message" => "Username and password are required"));
                }
            }
        );
    }
}