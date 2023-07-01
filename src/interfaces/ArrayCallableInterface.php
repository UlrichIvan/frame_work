<?php

namespace App\interface;

use Closure;

interface ArrayCallableInterface
{
      public function isCallable(array $cbs): void;
      public function add(Closure $cb): void;
}
