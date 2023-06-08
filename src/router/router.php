<?php

namespace App\Router;

use App\Exception\RouterException;
use App\Http\Request;
use App\Http\Response;
use App\interface\RouterInterface;

/**
 * class to manage routes
 */
class Router implements RouterInterface
{
      /**   
       * contents routes with mapping actions
       */
      private array $routesMap = [];

      public Request $request;
      public Response $response;

      public function __construct()
      {
            $this->request = new Request();
            $this->response = new Response();
      }



      // add new routes and action inside of routes mapped property
      public function  setRoutesMap(string $method, string $route, \Closure | string $action): ?self
      {
            try {
                  if (is_string($route) && !empty($route) && (is_string($action) || is_callable($action))) {
                        $this->routesMap[$method][$route] = $action;
                        return $this;
                  }
                  throw new RouterException(sprintf("Unexcept arguments on %s method!!!", $method));
            } catch (\Throwable $th) {
                  throw $th;
            }
      }

      public function getRoutesMap(): array
      {
            return $this->routesMap;
      }

      public function __call(string $method, array $arguments): mixed
      {
            try {
                  if (!in_array(strtoupper($method), Router::SUPPORTED_METHODS, true)) {
                        throw new RouterException(sprintf("Unexisting method $method call on %s ", Router::class));
                  }
                  $this->setRoutesMap(strtoupper($method), $arguments[0] === '/' ? '/' : rtrim($arguments[0], '/'), $arguments[1]);
                  return $this;
            } catch (RouterException $e) {
                  die($e->getMessage());
            }
      }

      public function run(): void
      {
            $request_method = $_SERVER['REQUEST_METHOD'];

            $request_uri = $_SERVER['REQUEST_URI'];

            try {
                  if (!in_array($request_method, Router::SUPPORTED_METHODS, true)) {
                        throw new RouterException(sprintf("Unexisting method $request_method call on %s ", Router::class));
                  }

                  $routes = $this->getRoutesMap()[$request_method];

                  foreach ($routes as $route => $action) {
                        if ($route === $request_uri && is_callable($action)) {
                              call_user_func($action, $this->request, $this->response);
                              exit;
                        }
                  }

                  http_response_code(404);
                  exit;
            } catch (\Throwable $th) {
                  throw $th;
            }

            var_dump($_SERVER);
      }
}
