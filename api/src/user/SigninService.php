<?php
/**
 * SigninService
 *  - Simple authentication service
 * @version 1.0.0
 *  - signin method that creates a DTO to return complete user and account
 */
namespace Api\User;

use Aelion\Dbal\Exception\IncorrectSqlExpressionException;
use Aelion\Dbal\Exception\NotFoundException;
use Aelion\Http\Request\Request;
use Aelion\Http\Response\Response;
use Aelion\Http\Response\HttpResponseStatus;
use Aelion\Http\Response\JsonResponse;
use Aelion\Registry\Registrable;

class SigninService implements Registrable {
    private UserRepository $repository;
    private Request $request;

    private function __construct(Request $request) {
        $this->request = $request;
        $this->repository = new UserRepository();
    }

    /**
     * @override
     * @see Registrable interface
     */
    public static function getInstance(Request $request): Registrable {
        return new SigninService($request);
    }

    public function signin(): Response {
        try {
            $username = $this->request->getPostParam('username');
            $password = $this->request->getPostParam('password');
            $userEntity = $this->repository->findByLoginAndPassword($username, $password);

            $roles = [];
            foreach ($userEntity->getRoles() as $role) {
                $roles[] = [
                    'id' => $role->getId(),
                    'role' => $role->getRole()
                ];
            }

            $payload = [
                'id' => $userEntity->getId(),
                'login' => $userEntity->getLogin(),
                'password' => $userEntity->getPassword(),
                'account' => [
                    'id' => $userEntity->getAccount()->getId(),
                    'lastname' => $userEntity->getAccount()->getLastname(),
                    'firstname' => $userEntity->getAccount()->getFirstname(),
                    'gender' => $userEntity->getAccount()->getGender()
                ],
                'roles' => $roles
            ];

            return new JsonResponse($payload, HttpResponseStatus::OK);
        } catch (IncorrectSqlExpressionException $e) {
            $response = new JsonResponse([
                'message' => $e->getMessage()
            ], HttpResponseStatus::InternalServerError);
            return $response;
        } catch (NotFoundException $e) {
            $response = new JsonResponse([
                'message' => $e->getMessage()
            ], HttpResponseStatus::NotFound);
            return $response;
        }
    }
}
