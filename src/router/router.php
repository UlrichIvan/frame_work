<?php

namespace App\Router;

use App\Exception\RouterException;
use App\Http\Request;
use App\Http\Response;
use App\interface\RouterInterface;
use Error;
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
            //                   "action" => "",
            //                   "middlewares"=>[]
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


      public function __call(string $method, array $arguments): mixed
      {
            try {
                  if (!empty($arguments[2])) {
                        $this->addRoutesMap(strtoupper($method), $arguments[0], $arguments[2]);
                        $this->addMiddlewareToRoute($arguments[0], $arguments[1], strtoupper($method));
                        return $this;
                  } else {
                        return $this->addRoutesMap(strtoupper($method), $arguments[0], $arguments[1]);
                  }
            } catch (RouterException $e) {
                  die($e->getMessage());
            }
      }

      public function setMiddlewareToRoute(string $url, $index, $middleware): ?self
      {
            $map = $this->routes[$url]["maps"][$index];

            $map["middlewares"]
                  = !empty($map["middlewares"]) ? [...$map["middlewares"], $middleware] : [$middleware];

            $this->routes[$url]["maps"][$index] = $map;

            return $this;
      }

      public function addMiddlewareToRoute(string $route, \Closure | string $middleware, ?string $method = null)
      {

            try {
                  if (
                        is_string($route) && !empty($route)
                        && (is_string($middleware) || is_callable($middleware))
                        && in_array($method, Router::SUPPORTED_METHODS, true)
                  ) {

                        $url = rtrim($route, $route === '/' ? '' : '/');

                        $map = $this->getMap($url, $method);

                        $mapIndex = $this->findMapIndex($url, $method);


                        if ($mapIndex === -1 || empty($map)) {
                              throw new Error("Unable to set middleware before route $route !!! line:" . __LINE__);
                        }

                        $this->setMiddlewareToRoute($url, $mapIndex, $middleware);

                        return $this;
                  }

                  throw new Error("Unexcept arguments values on middleware. => line:" . __LINE__);
            } catch (\Throwable $th) {
                  throw $th;
            }
      }




      public function addMiddleware(string $route, \Closure | string $middleware): ?self
      {
            try {
                  if (is_string($route) && !empty($route) && (is_string($middleware) || is_callable($middleware)) && empty($method)) {

                        $url = rtrim($route, $route === '/' ? '' : '/');

                        $middlewares = $this->getMiddleWares($url);

                        $this->routes[$url]["middlewares"] =
                              !empty($middlewares) ? [...$middlewares, $middleware] : [$middleware];

                        return $this;
                  }

                  throw new RouterException("Unexcept middleware value !!! line:" . __LINE__);
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
                        break;
                  }
            }

            return $routemap;
      }


      /**
       * Get specific route inside of many routes
       */
      public function findMapIndex(string $url, string $method): ?int
      {
            $maps = $this->routes[$url]['maps'] ?? [];
            $index = -1;

            foreach ($maps as $key => $map) {
                  if ($map["method"] === $method) {
                        $index = $key;
                  }
            }

            return $index;
      }

      public function run(): void
      {
            $request_method = $this->request->getRequestValue('method');

            $request_uri =   rtrim($this->request->getUri(), $this->request->getUri() === "/" ? "" : "/");

            try {
                  if (!in_array($request_method, Router::SUPPORTED_METHODS, true)) {
                        throw new RouterException(sprintf("Unexisting method $request_method call on %s ", Router::class));
                  }

                  // get all maps associate to current uri
                  $maps = $this->getMaps($request_uri);

                  // get all middlewares associate to current uri
                  $middlewares = $this->getMiddleWares($request_uri);

                  $routeMapped = null;

                  $routesMiddlewares = [];

                  // if maps is empty close request with 404 header method
                  if (empty($maps)) {
                        http_response_code(404);
                        exit;
                  }


                  // call all middlewares 
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
                              $routesMiddlewares = $map["middlewares"] ?? [];
                              break;
                        }
                  }

                  if (!empty($routeMapped) && empty($routesMiddlewares)) {
                        call_user_func($routeMapped->action, $this->request, $this->response);
                        exit;
                  } elseif (!empty($routeMapped) && !empty($routesMiddlewares)) {
                        foreach ($routesMiddlewares as $middleware) {
                              if (is_callable($middleware)) {
                                    call_user_func($middleware, $this->request, $this->response);
                              }
                        }
                        call_user_func($routeMapped->action, $this->request, $this->response);
                        exit;
                  } else {
                        http_response_code(404);
                        exit;
                  }
            } catch (\Throwable $th) {
                  throw $th;
            }
      }
}
