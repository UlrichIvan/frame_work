<?php

namespace App\interface;

/**  
 * Content all properties and valeurs necessary for Request Object
 */
interface RequestInterface
{
      // constants
      const METHODS = ["POST", "GET", "DELETE", "UPDATE", "PATCH"];
      const CONTENT_TYPE_URL_ENCODED = "application/x-www-form-urlencoded";
      const CONTENT_TYPE_XML = "application/xml";
      const CONTENT_TYPE_JAVASCRIPT = "application/javascript";
      const CONTENT_TYPE_OCTET_STREAM = "application/octet-stream";
      const CONTENT_TYPE_MULTIPART_FORM_DATA = "multipart/form-data";

      // header of functions 
      public function getHttpValues(): array;
      public function setRequestValues(array $server): void;
      public function get(string $requestKey, string $keyValue): ?string;
      public function hasContentType(string $type): bool;
}
