<?php

namespace App\Core\Types;

use App\Core\interface\ArrayMapInterface;

class ArrayMap extends \ArrayObject implements ArrayMapInterface
{

      public function add(mixed $key, mixed $value): self
      {
            $this->offsetSet($key, $value);
            return $this;
      }

      public function get(mixed $key): mixed
      {
            return $this->offsetExists($key) ? $this->offsetGet($key) : null;
      }

      public function has(mixed $key): bool
      {
            return $this->offsetExists($key);
      }
}
