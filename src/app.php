<?php

namespace App;

use App\Types\ArrayMap;
use App\Router\Router;
use Closure;
use InvalidArgumentException;

/**
 * manage application 
 */
class App extends Router
{
      public $name = "App";

      private static ArrayMap $Routers;


      function __construct()
      {
            parent::__construct();
      }

      public static function use(string $uri, Closure $cb): self
      {
            return new self();
      }
      public static function run(): void
      {
      }

      public function accept(string $url, Closure $cb): ?self
      {
            try {
                  if (empty($url) || !is_string($url) || !is_callable($cb)) {
                        throw new InvalidArgumentException("Invalid arguments set on " . __METHOD__ . " method");
                  }
                  $this->addMiddleware($url, $cb);
                  return $this;
            } catch (\InvalidArgumentException $e) {
                  die($e->getMessage());
            }
      }


      public function urlencoded(): Closure
      {
            return function () {
                  if ($this->request->hasContentTypeUrlencoded()) {
                        $this->request->fillBody()->fillQuery();
                  }
            };
      }

      /**
       * Get the value of Routers
       */
      public function getRouters()
      {
            return $this->Routers;
      }

      /**
       * Set the value of Routers
       *
       * @return  self
       */
      public function setRouters($Routers)
      {
            $this->Routers = $Routers;

            return $this;
      }
}
