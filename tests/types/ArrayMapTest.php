<?php

namespace App\tests\types;

use App\Types\ArrayMap;
use App\Types\Map;
use PHPUnit\Framework\TestCase;

final class ArrayMapTest extends TestCase
{

      public function testAddMethod()
      {
            $arraymap = new ArrayMap();

            $arraymap->add("/", 20);

            $this->assertSame(20, $arraymap->get("/"));
      }


      public function testhasMethod()
      {
            $arraymap = new ArrayMap();

            $this->assertFalse($arraymap->has("/"));
      }
}
