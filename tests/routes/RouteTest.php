<?php

namespace App\tests\routes;

use App\Routes\Route;
use App\Types\ArrayMap;
use App\Types\Map;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
      public function testRouteConstructor()
      {
            $route = new Route();

            $this->assertNotNull($route->getMiddlewares());
            $this->assertNotNull($route->getMaps());
      }

      public function testRouteGettersAndSetters()
      {
            $route = new Route();

            $route->setMaps(new ArrayMap([new Map("POST")]));

            $route->setMiddlewares(new ArrayMap([function () {
            }]));

            $this->assertNotEmpty($route->getMiddlewares());
            $this->assertNotEmpty($route->getMaps());
      }

      public function testGetMapMethod()
      {
            $route = new Route();

            $map1 = new Map(
                  "POST",
                  function () {
                  },
                  new ArrayMap([function () {
                  }])
            );

            $map2 = new Map(
                  "GET",
                  function () {
                  },
                  new ArrayMap([function () {
                  }])
            );

            $route->setMaps(new ArrayMap([$map1, $map2]));

            $map = $route->getMap("POST");

            $this->assertInstanceOf(Map::class, $map);

            $this->assertNotSame("GET", $map->getMethod());
      }

      public function testGetMapIndexSuccess()
      {
            $route = new Route();

            $map = new Map(
                  "POST",
                  function () {
                  },
                  new ArrayMap([function () {
                  }])
            );

            $route->setMaps(new ArrayMap([$map]));


            $mapIndex = $route->getMapIndex("POST");

            $this->assertEquals(0, $mapIndex);
      }

      public function testGetMapIndexFailed()
      {
            $route = new Route();

            $mapIndex = $route->getMapIndex("POST");

            $this->assertEquals(-1, $mapIndex);
      }

      public function testHasMapFailed()
      {
            $route = new Route();

            $hasMap = $route->hasMap("POST");

            $this->assertFalse($hasMap);
      }

      public function testHasMapSuccess()
      {
            $route = new Route();

            $map = new Map(
                  "POST",
                  function () {
                  },
                  new ArrayMap([function () {
                  }])
            );

            $route->setMaps(new ArrayMap([$map]));

            $hasMap = $route->hasMap("POST");

            $this->assertTrue($hasMap);
      }
}
