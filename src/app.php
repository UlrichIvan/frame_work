<?php

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Router\Router;
use Closure;
use Error;

/**
 * manage application 
 */
class App extends Router
{
      public $name = "App";

      private array $accpets = [];

      function __construct()
      {
            parent::__construct();
      }

      public function getAccepts(): array
      {
            return $this->accpets;
      }

      public function setAccepts(string $url, Closure $cb): void
      {
            try {
                  if (empty($url) || !is_string($url)) {
                        throw new Error("Invalid parameters set on accept method");
                  }
                  $this->addMiddleware($url, $cb);
            } catch (\Throwable $th) {
                  throw $th;
            }
      }



      public function accept(string $url, Closure $cb): self
      {
            $this->setAccepts($url, $cb);
            return $this;
      }

      public function urlencoded(): Closure
      {
            return function () {
                  if ($this->request->hasContentTypeUrlencoded()) {
                        $this->request->fillBody();
                  }
            };
      }
}
