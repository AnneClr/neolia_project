<?php
/**
 * Kernel
 *  Base class running first
 * @version 1.0.0
 *  - Instantiate router
 *  - Handle request
 *  - Return Response object
 */
namespace Aelion;

use Aelion\Router\Router;
use Aelion\Http\Request\Request;
use Aelion\Http\Response\Response;
use Aelion\Http\Response\JsonResponse;
use Aelion\Http\Response\HttpResponseStatus;
use Aelion\Router\Exception\NoRouteMatchingException;
use Aelion\Router\Exception\NoSuchFileException;
use Dotenv\Dotenv;

class Kernel {
    /**
     * Instance of the Kernel
     */
    private static ?Kernel $instance = null;

    /**
     * Internal router
     */
    private Router $router;

    private Request $request;

    private function __construct() {
        $this->setRouter();
        // Set environment vars
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    /**
     * Create a Kernel instance if not already exists
     * @return Kernel
     */
    public static function create(): Kernel {
        if (self::$instance === null) {
            self::$instance = new Kernel();
        }
        return self::$instance;
    }

    /**
     * Process incoming request and return Response object
     * @return Response|null
     */
    public function processRequest(): ?Response {
        try {
            // Gather necessary request data
            $server = $_SERVER;
            $post = $_POST;
            $get = $_GET;
            $cookie = $_COOKIE;
            $files = $_FILES;
            $request = $_REQUEST;
            
            // Instantiate Request object with necessary arguments
            $this->request = new Request(
                $this,
                $server,
                $post,
                $get,
                $cookie,
                $files,
                $request
            );

            // Process the request and get the response
            $response = $this->request->process();
        } catch (NoRouteMatchingException $e) {
            // Handle NoRouteMatchingException
            $payload = [
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($payload, HttpResponseStatus::NotFound);
        } catch (NoSuchFileException $e) {
            // Handle NoSuchFileException
            $payload = [
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse(string $payload, HttpResponseStatus::NotFound);
        }

        // Return the response object
        return $response;
    }

    /**
     * Get the Router instance
     * @return Router
     */
    public function getRouter(): Router {
        return $this->router;
    }

    /**
     * Set the Router instance
     */
    private function setRouter() {
        $this->router = new Router();
    }
}
