<?php

namespace App\Router;

use App\Exception\RouteException;
use App\Exception\RouterException;
use App\Http\Request;
use App\Http\Response;
use App\interface\RouterInterface;
use App\Routes\Route;
use App\Types\ArrayMap;
use App\Types\Map;
use Closure;
use Error;
use InvalidArgumentException;

/**
 * class to manage routes
 */
class Router implements RouterInterface
{
      /**   
       * contents routes with mapping actions
       */
      //  [
      //       // "/" => [
      //       //       "middlewares" => [],
      //       //       "maps" => [
      //       //             [
      //       //                   "method" => "",
      //       //                   "action" => "",
      //       //                   "middlewares"=>[]
      //       //             ]
      //       //       ]
      //       // ],
      // ];
      private ArrayMap $routes;

      public Request $request;
      public Response $response;

      public function __construct()
      {
            $this->request = new Request();

            $this->response = new Response();

            $this->setRoutes(new ArrayMap());
      }


      public function __call(string $method, array $args): mixed
      {
            try {
                  if (!in_array($method, self::SUPPORTED_METHODS, true)) {
                        throw new InvalidArgumentException("Call unexisting method " . __METHOD__ . " in " . __CLASS__ . " at line:" . __LINE__);
                  }

                  if (!is_string($args[0]) || empty($args[0]) || !is_callable($args[1]) || !empty($args[2]) && !is_callable($args[2])) {
                        throw new InvalidArgumentException("invalid arguments set on " . __METHOD__ . " in " . __CLASS__ . " at line:" . __LINE__);
                  }

                  if (!empty($args[2])) {

                        $this->addRoutes(strtoupper($method), $args[0], $args[2]);

                        $this->addMiddlewareToMap(strtoupper($method), $args[0], $args[1]);

                        return $this;
                  } else {
                        return $this->addRoutes(strtoupper($method), $args[0], $args[1]);
                  }
            } catch (RouterException $e) {
                  die($e->getMessage());
            }
      }

      /**
       * Add middleware to signle route
       */
      public function addMiddlewareToMap(string $method, string $route, \Closure $middleware): ?self
      {

            try {
                  if (!is_string($route) || empty($route) || !is_callable($middleware) || !in_array($method, self::SUPPORTED_METHODS, true)) {
                        throw new InvalidArgumentException("invalid arguments set to " . __CLASS__ . "  !!! line:" . __LINE__);
                  }

                  $uri = $this->clearUri($route);

                  // if route not exists create it and add new map
                  if (!$this->routes->has($uri)) {
                        $this->routes->add($uri, new Route(new ArrayMap(), new ArrayMap(new Map($method, null, new ArrayMap($middleware)))));
                        return $this;
                  }

                  // get existing route
                  $route = $this->routes->get($uri);

                  // get all maps from route
                  $maps = $route->getMaps();

                  // add new middleware
                  $maps->append(new Map($method, null, new ArrayMap($middleware)));

                  // set updated maps
                  $route->setMaps($maps);

                  $this->routes->add($uri, $route);

                  return $this;
            } catch (\Throwable $th) {
                  throw $th;
            }
      }


      /**
       * add new routes and action inside of routes mapped property
       */
      public function  addRoutes(string $method, string $route, \Closure $action): ?self
      {
            try {
                  // clean uri
                  $uri = $this->clearUri($route);

                  // if route not exists add it to routes
                  if (empty($this->routes->get($uri))) {
                        $this->routes->add($uri, new Route(new ArrayMap(), new ArrayMap(new Map($method, $action, new ArrayMap()))));
                        return $this;
                  }

                  throw new RouteException("Unable to duplicate route " . $route, 1);
            } catch (RouteException $e) {
                  die($e->getMessage());
            }
      }


      /**
       * Add global middleware to route
       */
      public function addMiddleware(string $route, \Closure $middleware): ?self
      {
            try {
                  if (!is_string($route) || empty($route) || !is_callable($middleware)) {
                        throw new InvalidArgumentException("invalid arguments set to " . __CLASS__ . "  !!! line:" . __LINE__);
                  }

                  $uri = $this->clearUri($route);

                  if (!$this->routes->has($uri)) {
                        $this->routes->add($uri, new Route(new ArrayMap($middleware), new ArrayMap()));
                  }

                  if ($this->routes->has($uri)) {

                        // get current existing route
                        $route = $this->routes->get($uri);

                        // get current existing middlewares
                        $middlewares = $route->getMiddleWares();

                        // add new middleware to list of middlewares
                        $middlewares->append($middleware);

                        // set new middleware to route
                        $route->setMiddlewares($middlewares);

                        // add route modified
                        $this->routes->add($uri, $route);
                  }

                  return $this;
            } catch (\InvalidArgumentException $e) {
                  die($e->getMessage());
            }
      }

      /**
       * return the clean uri value
       */
      public function clearUri(string $uri): string
      {
            return rtrim($uri, $uri === '/' ? '' : '/');
      }

      /**
       * Get middlewares of route assoc to uri paramter
       */
      public function getMiddleWares(string $uri): ?array
      {
            $route = $this->routes->get($uri);

            return !empty($route->getMiddleWares()) ? $route->getMiddleWares() : null;
      }


      /**
       * Get maps of route assoc to uri paramter
       */
      public function getMaps(string $uri): ?array
      {
            $route = $this->routes->get($uri);

            return !empty($route->getMaps()) ? $route->getMaps() : null;
      }


      /**
       * Get specific route inside of many routes
       */
      public function getMap(string $uri, string $method): ?array
      {

            if (!$this->routes->has($uri)) {
                  return null;
            }

            $map = $this->routes->get($uri)->getMap($method);

            return !empty($map) ? $map : null;
      }


      /**
       * Get specific route inside of many routes
       */
      public function findMapIndex(string $uri, string $method): ?int
      {
            if (!$this->routes->has($uri) || !empty($this->routes->get($uri)) && !$this->routes->get($uri)->hasMap($method)) {
                  return -1;
            }

            return $this->routes->get($uri)->getMapIndex($method);
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

      /**
       * Get all routes 
       */
      public function getRoutes(): ?ArrayMap
      {
            return $this->routes;
      }


      /**
       * Set the value of routes
       *
       * @return  self
       */
      public function setRoutes($routes)
      {
            $this->routes = $routes;

            return $this;
      }
}
