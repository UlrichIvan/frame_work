<?php

namespace App\Router;

use App\Exception\RouterException;
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

      // add new routes and action inside of routes mapped property
      public function  setRoutesMap(string $method, string $route, \Closure | string $action): self
      {
            $this->routesMap[$method][$route] = $action;
            return $this;
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
                  $this->setRoutesMap(strtoupper($method), $arguments[0], $arguments[1]);
                  return $this;
            } catch (RouterException $e) {
                  die($e->getMessage());
            }
      }
}
