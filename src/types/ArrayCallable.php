<?php

namespace App\Types;

use App\interface\ArrayCallableInterface;
use Closure;
use InvalidArgumentException;

final class ArrayCallable extends \ArrayObject implements ArrayCallableInterface
{

      public function __construct(array $cbs = [])
      {
            if (!empty($cbs)) {
                  $this->isCallable($cbs);
            }

            parent::__construct($cbs);
      }

      public function isCallable(array $cbs): void
      {
            try {
                  foreach ($cbs as $cb) {
                        if (!is_callable($cb)) {
                              throw new InvalidArgumentException("Invalid callbacks set,all elements of array callback must be callable", 1);
                        }
                  }
            } catch (\Throwable $th) {
                  throw $th;
            }
      }

      public function add(Closure $cb): void
      {
            $this->append($cb);
      }
}
