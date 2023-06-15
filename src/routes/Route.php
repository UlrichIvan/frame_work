<?php

namespace App\Routes;

use App\interface\RouteInterface;
use App\Types\ArrayMap;

class Route implements RouteInterface
{
      private  string $url;
      private  ArrayMap  $middlewares;
      private  ArrayMap  $maps;

      public function __construct(string $url, ArrayMap $middlewares, ArrayMap $maps)
      {
            $this->url = $url;
            $this->middlewares = $middlewares;
            $this->maps = $maps;
      }

      /**
       * Get the value of url
       */
      public function getUrl()
      {
            return $this->url;
      }

      /**
       * Set the value of url
       *
       * @return  self
       */
      public function setUrl($url)
      {
            $this->url = $url;

            return $this;
      }

      /**
       * Get the value of middlewares
       */
      public function getMiddlewares()
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

      /**
       * Get the value of maps
       */
      public function getMaps(): ArrayMap
      {
            return $this->maps;
      }

      /**
       * Set the value of maps
       *
       * @return  self
       */
      public function setMaps($maps)
      {
            $this->maps = $maps;

            return $this;
      }
}
