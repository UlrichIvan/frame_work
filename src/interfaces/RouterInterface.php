<?php

namespace App\interface;

use App\Routes\Route;

/**  
 * Interface to contains type of properties router and methods
 */
interface RouterInterface
{
      /** 
       *    list of methods supported
       */
      const SUPPORTED_METHODS = ["get", "post", "update", "patch", "delete", "put", "head"];

      /**
       * return the clean uri value
       */
      public function clearUri(string $uri): string;

      /**
       * set callback to retrive data entry from incoming request
       */
      public function before(string $uri, \Closure $cb): self;

      /**
       * verify if route exists on router Object
       */
      public function hasRoute(string $method, string $uri): bool;

      /**
       * get the route associate to method and incomming uri
       */
      public function getRoute(string $method, string $uri): ?Route;
}
