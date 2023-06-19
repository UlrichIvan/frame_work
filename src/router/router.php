<?php

namespace App\Router;

use App\Exception\RouteException;
use App\Exception\RouterException;
use App\Http\Request;
use App\Http\Response;
use App\interface\RouterInterface;
use App\Routes\Route;
use App\Types\ArrayMap;
use InvalidArgumentException;
use Closure;
use Error;

/**
 * class to manage routes
 */
class Router implements RouterInterface
{
      /**
       * content global callback from each route
       */
      private ArrayMap $accepts;

      /**   
       * contents routes with mapping actions
       */
      private ArrayMap $routes;

      /**   
       * contents Object Request
       */
      public Request $request;

      /**   
       * contents Object Response
       */
      public Response $response;

      public function __construct(Request $request = new Request(), Response $response = new Response())
      {
            $this->request = $request;

            $this->response = $response;

            $this->setRoutes(new ArrayMap());

            $this->setAccepts(new ArrayMap());
      }


      public function __call(string $method, array $params): mixed
      {
            try {
                  // only supported methods accepted
                  if (!in_array($method, self::SUPPORTED_METHODS, true)) {
                        throw new InvalidArgumentException("Call unexisting method '" . $method . "'");
                  }

                  // uri and callback only
                  if (is_string($params[0]) && is_callable($params[1]) && empty($params[2])) {
                        $this->addRoute($method, $params[0], $params[1], []);
                        return $this;
                  }

                  // uri and callback and middlewares
                  if (is_string($params[0]) && is_callable($params[1]) && !empty($params[2]) && (is_callable($params[2]) || is_array($params[2]))) {
                        $this->addRoute($method, $params[0], $params[1], $params[2]);
                        return $this;
                  }

                  throw new InvalidArgumentException("invalid arguments set on '" . $method . "' ");
            } catch (RouterException $e) {
                  die($e->getMessage());
            }
      }

      /**
       * check if route exists
       * @param $method [method associate to route]
       * @param $uri [uri associate to route]
       */
      public function hasRoute(string $method, string $uri): bool
      {
            $exists = false;

            foreach ($this->routes as $route) {
                  if ($route->getUri() === $uri && $route->getMethod() === $method) {
                        $exists = true;
                        break;
                  }
            }
            return $exists;
      }

      /**
       * add new routes and action inside of routes mapped property
       */
      private function addRoute(string $method, string $uri, \Closure $action, array | Closure $middlewares): ?self
      {
            try {
                  // clean uri
                  $uri = $this->clearUri($uri);

                  // verify if route with method exists
                  if ($this->hasRoute($method, $uri)) {
                        throw new Error("Unable to duplicate route' " . $uri . "' with method: '" . $method . "'");
                  }

                  // create new route with properties
                  $route = new Route($uri, $method, (is_callable($middlewares) ? [$middlewares] : $middlewares), $action);

                  // add new route
                  $this->routes->append($route);

                  return $this;
            } catch (RouteException $e) {
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


      public function ready(): void
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
      public function setRoutes(ArrayMap $routes): self
      {
            $this->routes = $routes;

            return $this;
      }

      /**
       * Get the value of accepts
       */
      public function getAccepts()
      {
            return $this->accepts;
      }

      /**
       * Set the value of accepts
       *
       * @return  self
       */
      public function setAccepts(ArrayMap $accepts): self
      {
            $this->accepts = $accepts;

            return $this;
      }

      public function accept(string $uri, Closure $cb): self
      {
            if ($this->accepts->has($uri)) {
                  $accepted = $this->accepts->get($uri);
                  $this->accepts->add($uri, [...$accepted, $cb]);
                  return $this;
            }
            $this->accepts->add($uri, [$cb]);

            return $this;
      }
}
