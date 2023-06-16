<?php

namespace App\tests\types;

use App\Types\ArrayMap;
use App\Types\Map;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
      public function testMapConstructor()
      {
            $map = new Map("POST", function () {
            }, new ArrayMap());

            $this->assertNotEmpty($map->getMethod());

            $this->assertNotEmpty($map->getAction());

            $this->assertNotNull($map->getMiddlewares());
      }

      public function testMapGettersAndSetters()
      {
            $map = new Map();

            $map->setMethod("POST");

            $map->setAction(function () {
            });

            $map->setMiddlewares(new ArrayMap());


            $this->assertSame("POST", $map->getMethod());

            $this->assertIsCallable($map->getAction());

            $this->assertInstanceOf(ArrayMap::class, $map->getMiddlewares());
      }

      public function testMapMethods()
      {
            $map = new Map();


            $this->assertFalse($map->hasMethod());

            $this->assertFalse($map->hasAction());
      }
}
