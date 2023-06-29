<?php

namespace App;

use App\Exception\RouterException;
use App\Types\ArrayMap;
use App\Routers\Router;
use Closure;
use InvalidArgumentException;

/**
 * manage application 
 */
class App extends Router
{
      public $name = "App";

      private ArrayMap $routers;


      function __construct()
      {
            $this->routers = new ArrayMap();
            parent::__construct();
      }

      public function use(string $prefix, Closure $cb): self
      {
            $this->routers->add($prefix, $cb());
            return $this;
      }


      public function run()
      {
            try {
                  // get uri from incoming method
                  $request_uri = $this->clearUri($this->request->getUri());

                  // match router associate to prefix
                  foreach ($this->routers as $prefix => $router) {
                        if (preg_match("#^" . $prefix . "#", $request_uri, $matched) && !empty($matched)) {
                              $uri = str_replace($matched[0], "", $request_uri);
                              $router->readyGo($uri === "" ? "/" : $uri);
                              break;
                        }
                  }
                  return $this->response->setStatus(404)->close();
            } catch (\Throwable $th) {
                  throw $th;
            }
      }
}
