<?php
/**
 * Http Request processing
 * @version 1.0.0
 *  - Simply gather Request information
 *  - Route to controller
 */
namespace Aelion\Http\Request;

use Aelion\Http\Request\Exception\NoSuchArgumentException;
use Aelion\Kernel;
use Aelion\Http\Request\Datas\PostData;
use Aelion\Http\Request\Datas\GetData;
use Aelion\Http\Request\Datas\PayloadData;
use Aelion\Http\Request\Exception\NoRouteMatchingException;
use Aelion\Http\Request\Exception\NoSuchFileException;
use Aelion\Http\Response\Response;
use Aelion\Router\TargetParser;
use Aelion\Router\ParsedRoute;

final class Request {
    private Kernel $kernel;

    /**
     * HTTP Method
     */
    private string $method;

    /**
     * Request path
     */
    private string $uri;

    /**
     * Request datas
     *  - POST, GET, JSON payload
     */
    private array $datas = [];
    private array $server;
    private array $post;
    private array $get;
    private array $cookie;
    private array $files;
    private array $request;

    public function __construct(Kernel $kernel, array $server, array $post, array $get, array $cookie, array $files, array $request) {
        $this->kernel = $kernel;
        $this->server = $server;
        $this->post = $post;
        $this->get = $get;
        $this->cookie = $cookie;
        $this->files = $files;
        $this->request = $request;
    }

    public function get($key, $value=null): string {
        if (array_key_exists($key, $this->datas)) {
            return $this->datas[$key];
        }
        throw new NoSuchArgumentException('Data : ' . $key . ' does not exists in this Http Request');
    }

    public function set(string $key, string $value): void {
        $this->datas[$key] = $value;
    }

    public function getPayload(): array {
        return $this->datas;
    }

    public function process(): Response {
        $this->setCorsHeaders();

        $this->method = $this->server['REQUEST_METHOD'];
        $this->uri = $this->server['REQUEST_URI'];

        $match = $this->kernel->getRouter()->match();

        if ($match !== false) {
            $this->setRequestDatas();
            $target = $match['target'];
            
            // Ensure the target is a string or process a Closure
            if ($target instanceof \Closure) {
                $target = $target(); // Call the Closure to get the string
            }

            if (is_string($target)) {
                $targetParser = new TargetParser($target);
                $parsedRoute = $targetParser->parse();
                if ($parsedRoute->checkPath()) {
                    require_once(__DIR__ . '/../../../' . $parsedRoute->getPath() . $parsedRoute->getController() . '.php');
                    $class = new \ReflectionClass($parsedRoute->fqClassName());
                    $controllerInstance = $class->newInstanceArgs([$this]);
                    $endpoint = $parsedRoute->getEndpoint();
                    return $controllerInstance->{$endpoint}();
                } else {
                    throw new NoSuchFileException('No controller file were found for : ' . $parsedRoute->getController() . '.php');
                }
            } else {
                throw new \InvalidArgumentException("Expected a string, got " . gettype($target));
            }
        } else {
            throw new NoRouteMatchingException('No candidate for ' . $this->uri . ' was found.');
        }
    }

    private function setRequestDatas(): void {
        $postData = new PostData($this);
        $postData->process();

        $getData = new GetData($this);
        $getData->process();

        $payloadData = new PayloadData($this);
        $payloadData->process();
    }

    private function setCorsHeaders() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, Content-Type');
    }

    private function getTarget() {
        // Example method returning a Closure for demonstration
        return function() {
            return "Namespace\\Controller#endpoint";
        };
    }
}
