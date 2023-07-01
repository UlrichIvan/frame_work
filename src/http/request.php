<?php

namespace App\Http;

use App\interface\RequestInterface;
use Exception;

/**
 * Class Request to manage entry request
 */
class Request implements RequestInterface
{
      private array $body = [];
      private array $query = [];
      private array $params = [];
      private array $httpValues = [
            "http" => [],
            "request" => [],
            "remote" => [],
            "server" => [],
            "system" => []
      ];

      private const PARAM_RGX = "#^(:[a-zA-Z_]+)$#";

      private string $currentUri = "";


      function __construct()
      {
            $this->setRequestValues($_SERVER);
      }

      public function __call($name, $arguments)
      {
            try {
                  if (preg_match("#^hasContentType#", $name, $matched)) {
                        $contentType = str_replace($matched[0], "", $name);
                        return $this->hasContentType(strtolower($contentType));
                  }
                  if (preg_match("#^get(Http|Request|Remote|Server|System)Value$#", $name, $matched)) {
                        $requestKey = strtolower(str_replace(["get", "value"], "", strtolower($matched[0])));
                        return $this->get($requestKey, $arguments[0]);
                  }
                  throw new Exception("Invalid method name call: '" . $name . "'", 1);
            } catch (\Throwable $th) {
                  throw $th;
            }
      }




      /**
       * Set body request value from incoming request which only has application/json value from content-type 
       */
      public function fillBody(): ?self
      {
            if (!empty($_POST)) {
                  foreach ($_POST as $key => $value) {
                        $this->setBodyValue($key, $value);
                  }
            }

            if (in_array($this->get("request", "method"), ["PUT", "PATCH", "DELETE"])) {

                  $putdata = $this->getInputData();

                  foreach ($putdata as $key => $value) {
                        $this->setBodyValue($key, $value);
                  }
            }

            return $this;
      }

      private function getInputData(): array
      {
            $putdata = fopen("php://input", "r");

            $result = "";

            while ($data = fread($putdata, 1024)) {
                  $result .= $data;
            }

            fclose($putdata);

            parse_str($result, $vars);

            return $vars;
      }

      /**
       * Set queries request value from incoming request
       */
      public function fillQuery(): ?self
      {
            if (!empty($_GET)) {
                  foreach ($_GET as $key => $value) {
                        $this->setQueryValue($key, $value);
                  }
            }
            return $this;
      }

      /**
       * return the body request from incoming request
       */
      public function getBody(): array
      {
            return $this->body;
      }

      /**
       * return the body value request from incoming request
       */
      public function getBodyValue(string $key): ?string
      {
            return !empty($this->body[$key]) ? $this->body[$key] : null;
      }

      /**
       * Get a specific value from server associate to key request and key value 
       */
      public function get(string $requestKey, string $keyValue): ?string
      {
            return !empty($this->httpValues[$requestKey][$keyValue]) ? $this->httpValues[$requestKey][$keyValue] : null;
      }

      /**
       * Get value request associate to key value
       */
      public function getPropertyValue(string $key): mixed
      {
            return !empty($this->$key) ? $this->$key : null;
      }

      /**
       * add value in request object 
       */
      public function add(string $key, mixed $value): ?self
      {
            $this->{$key} = $value;

            return $this;
      }


      /** 
       * Verify if request has a specific content type required by application
       */
      public function hasContentType(string $type): bool
      {

            if (Request::CONTENT_TYPE_URL_ENCODED === "application/x-www-form-" . $type) {
                  return true;
            }
            return false;
      }

      /**
       * set all values request from incoming request
       */
      public function setRequestValues(array $server): void
      {
            foreach ($server as $key => $item) {
                  if (preg_match('#HTTP_#', $key, $matched) === 1) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['http'][$field] = $item;
                  } else if (preg_match('#REQUEST_#', $key, $matched) === 1) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['request'][$field] = $item;
                  } else if (preg_match('#REMOTE_#', $key, $matched) === 1) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['remote'][$field] = $item;
                  } else if (preg_match('#SERVER_#', $key, $matched) === 1) {
                        $field = strtolower(str_replace($matched[0], "", $key));
                        $this->httpValues['server'][$field] = $item;
                  } else if (preg_match('#^QUERY_STRING$#', $key, $matched) === 1) {
                        $field = strtolower(str_replace("_STRING", "", $matched[0]));
                        $this->httpValues['request'][$field] = $item;
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

      /**
       * Get the value of query
       */
      public function getQuery(): ?array
      {
            return $this->query;
      }


      /**
       * Get the value of query
       */
      public function getQueryValue(string $key): ?string
      {
            return !empty($this->query[$key]) ? $this->query[$key] : null;
      }


      /**
       * Set the value of query
       *
       * @return  self
       */
      public function setQueryValue(string $key, string $value): ?self
      {
            $this->query[$key] = $value;

            return $this;
      }

      /**
       * Set the value of body
       *
       * @return  self
       */
      public function setBodyValue(string $key, string $value): ?self
      {
            $this->body[$key] = $value;

            return $this;
      }



      /**
       * retrun the uri without query string
       */
      public function getUri(): ?string
      {
            $query_string = $this->get("request", "query");
            $uri = $this->get("request", "uri");
            return !empty($query_string) ? str_replace("?" . $query_string, "", $uri) : $uri;
      }

      /**
       * Get the value of params
       */
      public function getParams()
      {
            return $this->params;
      }


      /**
       * verify if $uri has paramsNames
       */
      public function hasParamsNames(string $uri): bool
      {
            $found = false;

            foreach (explode("/", $uri) as $value) {
                  if (preg_match(self::PARAM_RGX, $value, $matched) === 1) {
                        $found = true;
                        break;
                  }
            }

            return $found;
      }

      /**
       * return the names of params from schema of uri
       */
      public function getParamsNamesIntoUri(string $uriSchema): array
      {

            $params = [];

            foreach (explode("/", $uriSchema) as $pos => $param_symbole) {
                  if (preg_match(self::PARAM_RGX, $param_symbole, $matched) === 1) {
                        $params[$pos] = str_replace(":", "", $param_symbole);
                  }
            }

            return $params;
      }

      /**
       * return array that contains uri valided and the params with values   
       */
      public function setParams(string $incomingUri, string $schemaUri, array $paramsNames): self
      {

            $paramsValues = [];

            // array of uri with values
            $partsofIncomingUri = explode("/", $incomingUri);

            // array of uri with params names
            $partsofShemaUri = explode("/", $schemaUri);

            if (count($partsofIncomingUri) === count($partsofShemaUri)) {

                  // assoc all params to value
                  foreach ($partsofIncomingUri as $key => $value) {
                        $partsofShemaUri[$key] = $value;
                        if (array_key_exists($key, $paramsNames)) {
                              $param_name = $paramsNames[$key];
                              $paramsValues[$param_name] = $partsofIncomingUri[$key];
                        }
                  }
            }

            if (implode("/", $partsofIncomingUri) === implode("/", $partsofShemaUri)) {
                  $this->params = $paramsValues;
                  $this->setCurrentUri($incomingUri);
            }

            return $this;
      }

      /**
       * Get the value of currentUri
       */
      public function getCurrentUri(): string
      {
            return $this->currentUri;
      }

      /**
       * Set the value of currentUri
       *
       * @return  self
       */
      public function setCurrentUri($currentUri): self
      {
            $this->currentUri = $currentUri;

            return $this;
      }
}
