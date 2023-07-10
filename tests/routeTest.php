<?php

namespace App\tests\routes;

use App\Routes\Route;
use App\Types\ArrayCallable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
      public function testRouteConstructorGettersAndSetters()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            }, ["id" => 1]);

            $this->assertSame("/", $route->getUri());
            $this->assertSame("post", $route->getMethod());
            $this->assertEmpty($route->getMiddlewares());
            $this->assertIsCallable($route->getAction());
            $this->assertSame(["id" => 1], $route->getParamsNames());
      }

      public function testGetterAndSetterFromUriProperty()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            }, ["id" => 1]);

            $route->setUri("/post");

            $this->assertSame("/post", $route->getUri());
      }

      public function testGetterAndSetterFromMethodProperty()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            }, ["id" => 1]);

            $route->setMethod("put");

            $this->assertSame("put", $route->getMethod());
      }

      public function testGetterAndSetterFromMiddlewaresPropertyPass()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            }, ["id" => 1]);

            $route->setMiddlewares(new ArrayCallable([
                  function () {
                        // code here...
                  }
            ]));

            $this->assertNotEmpty($route->getMiddlewares());
            $this->assertIsArray($route->getMiddlewares());
            $this->assertIsCallable($route->getMiddlewares()[0]);
      }

      public function testGetterAndSetterFromMiddlewaresPropertyWithException()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            }, ["id" => 1]);

            $this->expectException(InvalidArgumentException::class);

            $route->setMiddlewares(new ArrayCallable([
                  "test"
            ]));
      }

      public function testGetterAndSetterFromActionPropertyPass()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            });

            $this->assertIsCallable($route->getAction());
      }

      public function testGetterAndSetterFromParamsNamesPropertyPass()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            });

            $route->setParamsNames([
                  "id" => 1
            ]);

            $this->assertIsArray($route->getParamsNames());
            $this->assertCount(1, $route->getParamsNames());
            $this->assertSame([
                  "id" => 1
            ], $route->getParamsNames());
      }
}
