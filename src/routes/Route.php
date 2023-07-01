<?php

namespace App\Routes;

use App\interface\RouteInterface;
use App\Types\ArrayCallable;
use Closure;
use InvalidArgumentException;

class Route implements RouteInterface
{
      /**
       * uri of route
       */
      private string $uri;

      /**
       * names of params into route
       */
      private array $paramsNames = [];

      /**
       * method associate to route
       */
      private string $method;

      /** 
       * array of callbacks functions or callback function call before action of route
       */
      private  array $middlewares = [];


      /**
       * callback function call at the last time of route
       */
      private Closure $action;


      public function __construct(string $uri, string $method, array $middlewares, Closure $action, array $paramsNames = [])
      {
            $this->setUri($uri);
            $this->setMethod($method);
            $this->setMiddlewares(new ArrayCallable($middlewares));
            $this->setAction($action);
            $this->setParamsNames($paramsNames);
      }

      /**
       * Get uri of route
       */
      public function getUri(): string
      {
            return $this->uri;
      }

      /**
       * Set uri of route
       *
       * @return  self
       */
      public function setUri(string $uri): self
      {
            $this->uri = $uri;

            return $this;
      }

      /**
       * Get method associate to route
       */
      public function getMethod(): string
      {
            return $this->method;
      }

      /**
       * Set method associate to route
       *
       * @return  self
       */
      public function setMethod(string $method): self
      {
            $this->method = $method;

            return $this;
      }

      /**
       * Get array of callback functions call before action of route
       */
      public function getMiddlewares(): array
      {
            return $this->middlewares;
      }

      /**
       * Set array of callback functions call before action of route
       *
       * @return  self
       */
      public function setMiddlewares(ArrayCallable $middlewares): self
      {
            $this->middlewares = [...$middlewares];

            return $this;
      }

      /**
       * Get callback function call at the last time of request
       */
      public function getAction(): Closure
      {
            return $this->action;
      }

      /**
       * Set callback function call at the last time of request
       *
       * @return  self
       */
      public function setAction(Closure $action): self
      {
            $this->action = $action;

            return $this;
      }


      public function getParamsNames(): array
      {
            return $this->paramsNames;
      }

      /**
       * Set params of route
       *
       * @return  self
       */
      public function setParamsNames(array $paramsNames): self
      {
            $this->paramsNames = $paramsNames;

            return $this;
      }
}
