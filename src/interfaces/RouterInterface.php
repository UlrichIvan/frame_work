<?php

namespace App\interface;


/**  
 * Interface to contains type of properties router and methods
 */
interface RouterInterface
{
      const SUPPORTED_METHODS = ["GET", "POST", "UPDATE", "PATCH", "DELETE", "PUT"];
      public function getContentRoute(string $route): ?array;
      public function addRoutesMap(string $method, string $route, \Closure | string $action): ?self;
}
