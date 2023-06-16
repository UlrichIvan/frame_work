<?php

namespace App\interface;

use App\Types\Map;

interface RouteInterface
{
      /**
       * Get map associate with specific method
       */
      public function getMap(string $method): ?Map;

      /**
       * Get index of map associate with specific method
       */
      public function getMapIndex(string $method): int;

      /**
       * verify if map exits in maps
       */
      public function hasMap(string $method): ?bool;
}
