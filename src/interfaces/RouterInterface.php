<?php

namespace App\interface;


/**  
 * Interface to contains type of properties router and methods
 */
interface RouterInterface
{
      const SUPPORTED_METHODS = ["GET", "POST", "UPDATE", "PATCH", "DELETE"];
      public function getRoutesMap(): array;
      public function  setRoutesMap(string $method, string $route, \Closure | string $action): self;
}
