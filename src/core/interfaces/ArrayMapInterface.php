<?php

namespace App\Core\interface;

interface ArrayMapInterface
{
      public function add(mixed $key, mixed $value): self;
      public function get(mixed $key): mixed;
      public function has(mixed $key): bool;
}
