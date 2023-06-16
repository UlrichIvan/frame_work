<?php

namespace App\interface;


/**  
 * Interface to contains type of properties router and methods
 */
interface RouterInterface
{
      /** 
       *    list of methods supported
       */
      const SUPPORTED_METHODS = ["GET", "POST", "UPDATE", "PATCH", "DELETE", "PUT"];

      /**
       * add new routes and action inside of routes mapped property
       */
      public function  addRoutes(string $method, string $route, \Closure $action): ?self;


      /**
       * Add middleware to signle route
       */
      public function addMiddlewareToMap(string $method, string $route, \Closure $middleware): ?self;


      /**
       * Add global middleware to route
       */
      public function addMiddleware(string $route, \Closure $middleware): ?self;

      /**
       * return the clean uri value
       */
      public function clearUri(string $uri): string;

      /**
       * Get middlewares of route assoc to uri paramter
       */
      public function getMiddleWares(string $uri): ?array;

      /**
       * Get maps of route assoc to uri paramter
       */
      public function getMaps(string $uri): ?array;
}
