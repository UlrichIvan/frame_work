<?php

namespace App\Routes;

use App\interface\RouteInterface;
use App\Types\ArrayMap;
use App\Types\Map;

class Route implements RouteInterface
{
      private  ArrayMap  $middlewares;
      private  ArrayMap  $maps;

      public function __construct(ArrayMap $middlewares = new ArrayMap(), ArrayMap $maps = new ArrayMap())
      {
            $this->middlewares = $middlewares;
            $this->maps = $maps;
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
      public function setMaps(ArrayMap $maps)
      {
            $this->maps = $maps;

            return $this;
      }


      /**
       * Get map associate with specific method
       */
      public function getMap(string $method): ?Map
      {
            $maps = $this->getMaps();

            $mapFound = null;

            if (empty($maps)) {
                  return null;
            }

            foreach ($maps as $map) {
                  if ($map->getMethod() === $method) {
                        $mapFound = $map;
                        break;
                  }
            }

            return $mapFound;
      }


      /**
       * Get index of map associate with specific method
       */
      public function getMapIndex(string $method): int
      {
            $maps = $this->getMaps();

            $indexFound = -1;

            if (empty($maps)) {
                  return -1;
            }

            foreach ($maps as $index => $map) {
                  if ($map->getMethod() === $method) {
                        $indexFound = $index;
                        break;
                  }
            }

            return $indexFound;
      }


      /**
       * verify if map exits in maps
       */
      public function hasMap(string $method): ?bool
      {
            $maps = $this->getMaps();

            $exists = false;

            if (empty($maps)) {
                  return $exists;
            }

            foreach ($maps as $map) {
                  if (!empty($map) && $map->getMethod() === $method) {
                        $exists = true;
                        break;
                  }
            }

            return $exists;
      }
}
