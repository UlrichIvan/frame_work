<?php

namespace App\Router;

use App\Exception\RouterException;
use App\Http\Request;
use App\Http\Response;
use App\interface\RouterInterface;
use stdClass;

/**
 * class to manage routes
 */
class Router implements RouterInterface
{
      /**   
       * contents routes with mapping actions
       */
      private array $routes = [
            // "/" => [
            //       "middlewares" => [],
            //       "maps" => [
            //             [
            //                   "method" => "",
            //                   "action" => ""
            //             ]
            //       ]
            // ],
      ];

      public Request $request;
      public Response $response;

      public function __construct()
      {
            $this->request = new Request();
            $this->response = new Response();
      }



      public function addMiddleware(string $route, \Closure | string $middleware): ?self
      {
            try {
                  if (is_string($route) && !empty($route) && (is_string($middleware) || is_callable($middleware))) {

                        $url = rtrim($route, $route === '/' ? '' : '/');

                        $middlewares = $this->getMiddleWares($url);

                        $this->routes[$url]["middlewares"] =
                              !empty($middlewares) ? [...$middlewares, $middleware] : [$middleware];

                        return $this;
                  }
                  throw new RouterException(sprintf("Unexcept middleware value %s !!!", $middleware));
            } catch (\Throwable $th) {
                  throw $th;
            }
      }

      /**
       * add new routes and action inside of routes mapped property
       */
      public function  addRoutesMap(string $method, string $route, \Closure | string $action): ?self
      {
            try {
                  if (!in_array(strtoupper($method), Router::SUPPORTED_METHODS, true)) {
                        throw new RouterException(sprintf("Unexisting method $method call on %s ", Router::class));
                  }

                  if (is_string($route) && !empty($route) && (is_string($action) || is_callable($action))) {

                        $url = rtrim($route, $route === "/" ? "" : "/");

                        $maps = $this->getMaps($url);

                        $this->routes[$url]["maps"] =  !empty($maps) ? [...$maps, ["method" => $method, "action" => $action]] : [["method" => $method, "action" => $action]];

                        return $this;
                  }
                  throw new RouterException(sprintf("Unexcept arguments on %s method!!!", $method));
            } catch (\Throwable $th) {
                  throw $th;
            }
      }

      public function getMiddleWares(string $url): ?array
      {
            return $this->routes[$url]["middlewares"];
      }

      /**
       * Get route with method request
       */
      public function getContentRoute(string $route): ?array
      {
            return $this->routes[$route];
      }

      /**
       * Get maps from url
       */
      public function getMaps(string $route): ?array
      {
            return $this->routes[$route]['maps'];
      }

      /**
       * Get all routes 
       */
      public function getRoutes(): ?array
      {
            return $this->routes;
      }

      /**
       * Get specific route inside of many routes
       */
      public function getMap(string $url, string $method): ?array
      {
            $maps = $this->routes[$url]['maps'] ?? [];
            $routemap = null;

            foreach ($maps as $map) {
                  if ($map["method"] === $method) {
                        $routemap = $map;
                  }
            }

            return $routemap;
      }

      public function __call(string $method, array $arguments): mixed
      {
            try {
                  $this->addRoutesMap(strtoupper($method), $arguments[0], $arguments[1]);
                  return $this;
            } catch (RouterException $e) {
                  die($e->getMessage());
            }
      }

      public function run(): void
      {
            $request_method = $this->request->getRequestValue('method');

            $request_uri =   rtrim($this->request->getRequestValue('uri'), $this->request->getRequestValue('uri') === "/" ? "" : "/");

            try {
                  if (!in_array($request_method, Router::SUPPORTED_METHODS, true)) {
                        throw new RouterException(sprintf("Unexisting method $request_method call on %s ", Router::class));
                  }

                  // get all maps associate to current uri
                  $maps = $this->getMaps($request_uri);

                  // get all middlewares associate to current uri
                  $middlewares = $this->getMiddleWares($request_uri);

                  $routeMapped = null;

                  // if maps is empty close request with 404 header method
                  if (empty($maps)) {
                        http_response_code(404);
                        exit;
                  }

                  // call first all middlewares
                  if (is_array($middlewares) && !empty($middlewares)) {
                        foreach ($middlewares as $middleware) {
                              if (is_callable($middleware)) {
                                    call_user_func($middleware, $this->request, $this->response);
                              }
                        }
                  }

                  // match corresponding map
                  foreach ($maps as $map) {
                        if ($map['method'] === $request_method) {
                              $routeMapped = (object) $map;
                              break;
                        }
                  }

                  if (!empty($routeMapped)) {
                        call_user_func($routeMapped->action, $this->request, $this->response);
                        exit;
                  } else {
                        http_response_code(404);
                        exit;
                  }
            } catch (\Throwable $th) {
                  throw $th;
            }

            var_dump($_SERVER);
      }
}
