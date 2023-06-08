<?php

namespace App\Http;

use App\interface\RequestInterface;

/**
 * Class Request to manage entry request
 */
class Request implements RequestInterface
{
      // private array $body = [];
      // private array $query = [];
      // private array $params = [];
      private array $httpValues = [
            "http" => [],
            "request" => [],
            "remote" => [],
            "server" => [],
            "system" => []
      ];


      function __construct()
      {
            $this->setHttpValues($_SERVER);
      }

      // public function setBodyResquest()
      // {
      //       if ($_SERVER["HTTP_METHOD"] === "POST") {
      //             $this->body = $_POST;
      //       }
      // }
      public function getHttpValue(string $path): ?array
      {
            return $this->httpValues[$path];
      }

      public function setHttpValues(array $server): void
      {
            foreach ($server as $key => $item) {
                  if (preg_match('#HTTP_#', $key, $matched)) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['http'][$field] = $item;
                  } else if (preg_match('#REQUEST_#', $key, $matched)) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['request'][$field] = $item;
                  } else if (preg_match('#REMOTE_#', $key, $matched)) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['remote'][$field] = $item;
                  } else if (preg_match('#SERVER_#', $key, $matched)) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['server'][$field] = $item;
                  } else {
                        $field = strtolower($key);
                        $this->httpValues['system'][$field] = $item;
                  }
            }
      }

      /**
       * Get the value of httpValues
       */
      public function getHttpValues(): array
      {
            return $this->httpValues;
      }
}
