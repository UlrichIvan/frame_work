<?php

namespace App\Http;

use App\interface\RequestInterface;

/**
 * Class Request to manage entry request
 */
class Request implements RequestInterface
{
      private array $body = [];
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
            $this->setRequestValues($_SERVER);
      }

      public function __call($name, $arguments): mixed
      {
            if (preg_match("#^hasContentType#", $name, $matched)) {
                  $contentType = str_replace($matched[0], "", $name);
                  return $this->hasContentType(strtolower($contentType));
            }
            if (preg_match("#^get(Http|Request|Remote|Server|System)Value$#", $name, $matched)) {
                  $requestKey = strtolower(str_replace(["get", "value"], "", strtolower($matched[0])));
                  return $this->get($requestKey, $arguments[0]);
            }
      }

      /**
       * Set body request value from incoming request which only has application/json value from content-type 
       */
      public function fillBody(): void
      {
            foreach ($_POST as $key => $value) {
                  $this->body[$key] = $value;
            }
      }

      /**
       * return the body request from incoming request
       */
      public function getBody(): array
      {
            return $this->body;
      }

      /**
       * Get a specific value from server associate to key request and key value 
       */
      public function get(string $requestKey, string $keyValue): ?string
      {
            return $this->httpValues[$requestKey][$keyValue];
      }

      /** 
       * Verify if request has a specific content type required by application
       */
      public function hasContentType(string $type): bool
      {
            $contentType = $this->get("http", "content_type");

            if (Request::CONTENT_TYPE_URL_ENCODED === "application/x-www-form-" . $type) {
                  return true;
            }
            return false;
      }

      public function setRequestValues(array $server): void
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
