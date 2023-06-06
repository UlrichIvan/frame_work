<?php

namespace App\Http;

/**
 * Class Request to manage entry request
 */
class Request
{
      // public ?string $method;
      // public ?string $protocol;
      // public $time = null;
      // public $query = null;
      // public $mode = "http";
      // public $remote_address;
      // private $method = null;


      function __construct()
      {
            $this->setHttpValuesRequest();
      }

      private function setHttpValuesRequest()
      {
            foreach ($_SERVER as $key => $item) {
                  $field = strtolower($key);
                  $this->$field = $item;
            }
      }
}
