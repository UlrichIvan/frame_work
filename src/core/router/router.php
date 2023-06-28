<?php

namespace App\Core\Router;

use App\Core\Exception\RouteException;
use App\Core\Exception\RouterException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\interface\RouterInterface;
use App\Core\Routes\Route;
use App\Core\Types\ArrayMap;
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
      private ArrayMap $befores;

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

            $this->setBefores(new ArrayMap());
      }


      public function __call(string $method, array $params)
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
      private function addRoute(string $method, string $uri, Closure $action, array | Closure $middlewares): ?self
      {
            try {
                  // clean uri
                  $uri = $this->clearUri($uri);

                  // verify if route with method exists
                  if ($this->hasRoute($method, $uri)) {
                        throw new Error("Unable to duplicate route' " . $uri . "' with method: '" . $method . "'");
                  }

                  // create new route with properties
                  $route = new Route($uri, $method, (is_callable($middlewares) ? [$middlewares] : [...$middlewares]), $action);

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
      /**
       * get the route associate to method and uri
       */
      public function getRoute(string $method, string $uri): ?Route
      {
            $route = null;

            foreach ($this->routes as $_route) {
                  if ($_route->getMethod() === $method && $_route->getUri() === $uri) {
                        $route = $_route;
                        break;
                  }
            }

            return $route;
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
       * Get the value of befores
       */
      public function getBefores()
      {
            return $this->befores;
      }

      /**
       * Set the value of befores
       *
       * @return  self
       */
      public function setBefores(ArrayMap $befores): self
      {
            $this->befores = $befores;

            return $this;
      }

      /**
       * return the accept callback associate to uri
       */
      public function getBefore(string $uri): array
      {
            return $this->befores->has($uri) ? $this->befores->get($uri) : [];
      }

      public function before(string $uri, Closure|array $cb): self
      {
            if ($this->befores->has($uri)) {

                  $befores = $this->befores->get($uri);

                  $this->befores->add($uri, is_callable($cb) ? [...$befores, $cb] : [...$befores, ...$cb]);

                  return $this;
            }

            $this->befores->add($uri, is_callable($cb) ? [$cb] : [...$cb]);

            return $this;
      }

      public function ready()
      {
            // get method from incoming resquest
            $request_method = strtolower($this->request->getRequestValue('method'));

            // get uri from incoming method
            $request_uri = $this->clearUri($this->request->getUri());

            try {
                  if (!in_array($request_method, Router::SUPPORTED_METHODS, true)) {
                        throw new RouterException(sprintf("Unsupported method '" . $request_method . "' from request send"));
                  }

                  // get route associate to uri and method from incoming request 
                  $route = $this->getRoute($request_method, $request_uri);

                  if (empty($route)) {
                        return $this->response->setStatus(404)->close();
                  }


                  // get all middlewares associate to current uri
                  $middlewares = [...$this->getBefore($request_uri), ...$route->getMiddlewares()];

                  // first time,call all middlewares 
                  if (!empty($middlewares) && is_array($middlewares)) {
                        foreach ($middlewares as $middleware) {
                              if (is_callable($middleware)) {
                                    call_user_func($middleware, $this->request, $this->response);
                              }
                        }
                  }

                  call_user_func($route->getAction(), $this->request, $this->response);
            } catch (\Throwable $th) {
                  throw $th;
            }
      }
}
