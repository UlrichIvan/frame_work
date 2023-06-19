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
      const SUPPORTED_METHODS = ["get", "post", "update", "path", "delete", "put"];

      /**
       * return the clean uri value
       */
      public function clearUri(string $uri): string;

      /**
       * set callback to retrive data entry from incoming request
       */
      public function accept(string $uri, \Closure $cb): self;
}
