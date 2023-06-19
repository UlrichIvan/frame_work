<?php

namespace App\tests\routes;

use App\Routes\Route;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
      public function testRouteConstructorGettersAndSetters()
      {
            $route = new Route("/", "post", [], function () {
                  // do something...
            });

            $this->assertSame("/", $route->getUri());
            $this->assertSame("post", $route->getMethod());
            $this->assertEmpty($route->getMiddlewares());
            $this->assertIsCallable($route->getAction());
      }
}
