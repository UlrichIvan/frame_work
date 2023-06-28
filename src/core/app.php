<?php

namespace App;

use App\Router\Router;
use Closure;
use InvalidArgumentException;

/**
 * manage application 
 */
class App extends Router
{
      public $name = "App";


      function __construct()
      {
            parent::__construct();
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
}
