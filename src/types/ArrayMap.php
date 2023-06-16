<?php

namespace App\Types;

use App\interface\ArrayMapInterface;

class ArrayMap extends \ArrayObject implements ArrayMapInterface
{

      public function add(mixed $key, mixed $value): self
      {
            $this->offsetSet($key, $value);
            return $this;
      }

      public function get(mixed $key): mixed
      {
            return $this->offsetGet($key);
      }

      public function has(mixed $key): bool
      {
            return $this->offsetExists($key);
      }
}
