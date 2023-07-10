<?php

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\interface\RequestInterface;
use App\interface\ResponseInterface;
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

      private string $currentPrefix = "";

      private ArrayMap $routers;

      private ArrayMap $beforeIts;

      function __construct(RequestInterface $request = new Request(), ResponseInterface $response = new Response())
      {
            $this->routers = new ArrayMap();
            $this->beforeIts = new ArrayMap();

            parent::__construct($request, $response);
      }

      public function use(string $prefix, Closure $cb): self
      {
            $this->routers->add($prefix, $cb());
            return $this;
      }

      public function beforeIt(string $prefix): self
      {
            $this->setCurrentPrefix($prefix);

            return $this;
      }
      /**
       * @params Closure|array $cb [the callack or array of callback]
       * @params string $prefix [the prefix uri]
       */
      public function do(Closure|array $cb, ?string $prefix = null): self
      {
            try {
                  if ($this->currentPrefix === "" && (empty($prefix) || $prefix == "" || !is_string($prefix))) {
                        throw new InvalidArgumentException("An prefix value must not be empty,please set it before to added action to method " . __METHOD__, 1);
                  }

                  $this->setCurrentPrefix($prefix ? $prefix : $this->currentPrefix);

                  $this->beforeIts->add($this->getCurrentPrefix(), is_callable($cb) ? [$cb] : [...$cb]);

                  return $this;
            } catch (\Throwable $th) {
                  throw $th;
            }
      }

      public function run()
      {
            try {
                  // get uri from incoming method
                  $request_uri = $this->clearUri($this->request->getUri());

                  // match router associate to prefix
                  foreach ($this->routers as $prefix => $router) {
                        if (preg_match("#^" . $prefix . "#", $request_uri, $matched) === 1 && !empty($matched)) {
                              $uri = str_replace($matched[0], "", $request_uri);

                              // if exists, execute beforeIts action associate to current prefix uri found

                              if ($this->beforeIts->has($prefix)) {

                                    foreach ($this->beforeIts->get($prefix) as $cb) {
                                          call_user_func($cb, $this->request, $this->response);
                                    }
                              }

                              // call router found associate to prefix uri
                              $router->readyGo($uri === "" ? "/" : $uri);

                              break;
                        }
                  }
                  return $this->response->status(404);
            } catch (\Throwable $th) {
                  throw $th;
            }
      }



      /**
       * Get the value of routers
       */
      public function getRouters(): ArrayMap
      {
            return $this->routers;
      }

      /**
       * Set the value of routers
       *
       * @return  self
       */
      public function setRouters(ArrayMap $routers)
      {
            $this->routers = $routers;

            return $this;
      }

      /**
       * Get the value of currentPrefix
       */
      public function getCurrentPrefix(): string
      {
            return $this->currentPrefix;
      }

      /**
       * Set the value of currentPrefix
       *
       * @return  self
       */
      public function setCurrentPrefix(string $currentPrefix): self
      {
            $this->currentPrefix = $currentPrefix;

            return $this;
      }

      /**
       * Get the value of beforeIts
       */
      public function getBeforeIts(): ArrayMap
      {
            return $this->beforeIts;
      }
}
