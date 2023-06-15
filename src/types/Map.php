<?php

use App\Types\ArrayMap;

class Map
{
      private string $method;
      private \Closure $action;
      private ArrayMap $middlewares;

      /**
       * Get the value of method
       */
      public function getMethod()
      {
            return $this->method;
      }

      /**
       * Set the value of method
       *
       * @return  self
       */
      public function setMethod($method)
      {
            $this->method = $method;

            return $this;
      }

      /**
       * Get the value of action
       */
      public function getAction()
      {
            return $this->action;
      }

      /**
       * Set the value of action
       *
       * @return  self
       */
      public function setAction($action)
      {
            $this->action = $action;

            return $this;
      }

      /**
       * Get the value of middlewares
       */
      public function getMiddlewares(): ArrayMap
      {
            return $this->middlewares;
      }

      /**
       * Set the value of middlewares
       *
       * @return  self
       */
      public function setMiddlewares($middlewares)
      {
            $this->middlewares = $middlewares;

            return $this;
      }
}
