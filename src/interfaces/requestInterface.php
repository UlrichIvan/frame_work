<?php

namespace App\interface;

/**  
 * Content all properties and valeurs necessary for Request Object
 */
interface RequestInterface
{
      // constants
      /** 
       *    list of methods supported
       */
      const SUPPORTED_METHODS = ["get", "post", "update", "patch", "delete", "put", "head"];


      // header of functions 
      public function getHttpValues(): array;
      public function setRequestValues(array $server): void;
      public function get(string $requestKey, string $keyValue): ?string;
}
