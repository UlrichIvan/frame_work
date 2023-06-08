<?php

namespace App\interface;

/**  
 * Content all properties and valeurs necessary for Request Object
 */
interface RequestInterface
{
      const METHODS = ["POST", "GET", "DELETE", "UPDATE", "PATCH"];
      public function getHttpValues(): array;
      public function setHttpValues(array $server): void;
      public function getHttpValue(string $path): ?array;
}
