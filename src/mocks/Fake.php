<?php

namespace App\mocks;

use InvalidArgumentException;

/** 
 * manage fake class
 */
class Fake
{
      public static function mockClass(object $o = null, array $mockMethods = []): object
      {

            // object must be exists
            if ($o === null) {
                  throw new InvalidArgumentException("object must be not null!!!");
            }

            // verify that data are valid
            foreach ($mockMethods as $methodName => $mockImplementation) {
                  if (!is_string($methodName) || !is_callable($mockImplementation)) {
                        throw new InvalidArgumentException("Invalid mocks Methods send. format is array associate method => mockImplementation");
                  }
            }

            // Override the function associate to object
            foreach ($mockMethods as $methodName => $mockImplementation) {
                  $o->{$methodName} = $mockImplementation->bindTo($o, $o);
            }

            return $o;
      }
}
