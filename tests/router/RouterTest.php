<?php

namespace App\tests\router;

use App\Http\Request;
use App\Http\Response;
use App\Router\Router;
use App\Types\ArrayMap;
use Error;
use ErrorException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
      public function testRouterConstructor()
      {
            $router = new Router();

            $this->assertInstanceOf(Request::class, $router->request);
            $this->assertInstanceOf(Response::class, $router->response);
            $this->assertInstanceOf(ArrayMap::class, $router->getRoutes());
      }

      public function testAddMiddleware()
      {
            $router = new Router();

            $router->addMiddleware("/", function () {
                  // do something. here...
            });


            $router->addMiddleware("/", function () {
                  // do something. here...
            });

            $route = $router->getRoutes()->get('/');


            $this->assertTrue($router->getRoutes()->has("/"));
            $this->assertEquals(2, $route->getMiddlewares()->count());
      }

      public function testAddMiddlewareWithException()
      {
            $router = new Router();

            $this->expectException(Error::class);
            $router->addMiddleware("/", null);
      }

      public function testClearUri()
      {
            $router = new Router();

            $this->assertSame("/post", $router->clearUri("/post/"));
      }
}
