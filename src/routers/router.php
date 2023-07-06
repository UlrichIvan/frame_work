<?php

namespace App\Routers;

use App\Exception\RouteException;
use App\Exception\RouterException;
use App\Http\Request;
use App\Http\Response;
use App\interface\RequestInterface;
use App\interface\ResponseInterface;
use App\interface\RouterInterface;
use App\Routes\Route;
use App\Types\ArrayCallable;
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

      public function __construct(RequestInterface $request = new Request(), ResponseInterface $response = new Response())
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
                        return $this->addRoute($method, $params[0], $params[1], new ArrayCallable());
                  }

                  // uri and callback and middlewares
                  if (is_string($params[0]) && is_callable($params[1]) && !empty($params[2]) && (is_callable($params[2]) || is_array($params[2]))) {
                        return $this->addRoute($method, $params[0], $params[1], is_callable($params[2]) ? $params[2] :  new ArrayCallable($params[2]));
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
      private function addRoute(string $method, string $uri, Closure $action, ArrayCallable | Closure $middlewares): ?self
      {
            try {
                  // get paramsName into uri if they exist
                  $paramsNames = $this->request->hasParamsNames($uri) ? $this->request->getParamsNamesIntoUri($uri) : [];

                  // clean uri
                  $uri = $this->clearUri($uri);

                  // verify if route with method exists
                  if ($this->hasRoute($method, $uri)) {
                        throw new Error("Unable to duplicate route' " . $uri . "' with method: '" . $method . "'");
                  }

                  // create new route with properties
                  $route = new Route($uri, $method, (is_callable($middlewares) ? [$middlewares] : [...$middlewares]), $action, $paramsNames);

                  $this->routes->append($route);

                  return $this;
            } catch (RouteException $e) {
                  die($e->getMessage());
            } catch (InvalidArgumentException $e) {
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
       * get the route associate to method and incomming uri
       */
      public function getRoute(string $method, string $uri): ?Route
      {
            $route = null;

            foreach ($this->routes as $_route) {

                  if (!empty($_route->getParamsNames())) {
                        $this->request->setParams($uri, $_route->getUri(), $_route->getParamsNames());
                  }

                  // match route without params and route with params
                  if ($_route->getMethod() === $method && ($this->request->getCurrentUri() === $uri || $_route->getUri() === $uri)) {
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
            $callback = is_callable($cb) ? $cb : new ArrayCallable($cb);

            if ($this->befores->has($uri)) {

                  $befores = $this->befores->get($uri);

                  $this->befores->add($uri, is_callable($callback) ? [...$befores, $callback] : [...$befores, ...$callback]);

                  return $this;
            }

            $this->befores->add($uri, is_callable($callback) ? [$callback] : [...$callback]);

            return $this;
      }


      public function readyGo(string $uri_from_prefix = "")
      {
            try {
                  // get method from incoming resquest
                  $request_method = strtolower($this->request->getRequestValue('method'));

                  // get uri from incoming method
                  $request_uri = $uri_from_prefix !== "" ? $uri_from_prefix : $this->clearUri($this->request->getUri());

                  if (!in_array($request_method, Router::SUPPORTED_METHODS, true)) {
                        throw new RouterException(sprintf("Unsupported method '" . $request_method . "' from request send"));
                  }

                  // get route associate to uri and method from incoming request 
                  $route = $this->getRoute($request_method, $request_uri);

                  if (empty($route)) {
                        return $this->response->status(404)->close();
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
