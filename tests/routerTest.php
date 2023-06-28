<?php

namespace App\tests\routers;

use App\Http\Request;
use App\Http\Response;
use App\Router\Router;
use App\Routes\Route;
use App\Types\ArrayMap;
use PHPUnit\Framework\TestCase;


final class RouterTest extends TestCase
{
      public function testRouterConstructor()
      {
            $router = new Router();

            $this->assertInstanceOf(Request::class, $router->request);
            $this->assertInstanceOf(Response::class, $router->response);
            $this->assertInstanceOf(ArrayMap::class, $router->getRoutes());
            $this->assertEmpty($router->getRoutes());
      }

      public function testAddGlobalMiddleware()
      {
            $router = new Router();

            // add middlewares
            $router->accept("/", [function () {
                  // do something...
            }, function () {
                  // do something...
            }]);


            $this->assertNotEmpty($router->getAccepts());
            $this->assertCount(2, $router->getAccept("/"));
            $this->assertIsCallable($router->getAccept("/")[0]);
            $this->assertIsCallable($router->getAccept("/")[1]);
      }

      public function testAddMiddlewareToRoute()
      {
            $router = new Router();

            $router->post(
                  "/",
                  function (Request $req, Response $res) {
                        $res->json(["body" => $req->getBody()]);
                  },
                  function () {
                  }
            );

            $route = $router->getRoute("post", "/");

            $this->assertCount(0, $router->getAccept("/"));
            $this->assertInstanceOf(Route::class, $route);
            $this->assertCount(1, $route->getMiddlewares());
            $this->assertIsCallable($route->getMiddlewares()[0]);
      }

      public function testAddRouteWithoutMiddlewares()
      {
            $router = new Router();

            $router->post("/", function () {
                  // do something...
            });

            $route = $router->getRoute("post", "/");

            $this->assertInstanceOf(Route::class, $route);
            $this->assertSame($route->getMethod(), "post");
            $this->assertSame($route->getUri(), "/");
            $this->assertCount(0, $route->getMiddlewares());
      }

      public function testAddRouteWithCallbackMiddleware()
      {
            $router = new Router();

            $router->post("/", function () {
                  // do something...
            }, function () {
                  // do something...
            });

            $route = $router->getRoutes()[0];

            $this->assertInstanceOf(Route::class, $route);
            $this->assertSame($route->getMethod(), "post");
            $this->assertSame($route->getUri(), "/");
            $this->assertCount(1, $route->getMiddlewares());
            $this->assertIsCallable($route->getMiddlewares()[0]);
      }

      public function testAddRouteWithArrayMiddlewares()
      {
            $router = new Router();

            $router->post("/", function () {
                  // do something...
            }, [function () {
                  // do something...
            }, function () {
                  // do something...
            }]);

            $route = $router->getRoutes()[0];

            $this->assertInstanceOf(Route::class, $route);
            $this->assertSame($route->getMethod(), "post");
            $this->assertSame($route->getUri(), "/");
            $this->assertCount(2, $route->getMiddlewares());
            $this->assertIsCallable($route->getMiddlewares()[0]);
            $this->assertIsCallable($route->getMiddlewares()[1]);
      }

      public function testAddManyRoute()
      {
            $router = new Router();

            $router->post("/", function () {
                  // do something...
            });

            $router->get("/post", function () {
                  // do something...
            });

            $routes = $router->getRoutes();

            $this->assertCount(2, $routes);

            // instances
            $this->assertInstanceOf(Route::class, $routes[0]);
            $this->assertInstanceOf(Route::class, $routes[1]);

            // methods
            $this->assertSame($routes[0]->getMethod(), "post");
            $this->assertSame($routes[1]->getMethod(), "get");

            // uri from each route
            $this->assertSame($routes[0]->getUri(), "/");
            $this->assertSame($routes[1]->getUri(), "/post");

            // action from each route
            $this->assertIsCallable($routes[0]->getAction());
            $this->assertIsCallable($routes[1]->getAction());
      }

      public function testHasRoute()
      {
            $router = new Router();

            $router->post("/", function () {
                  // do something...
            });

            $this->assertTrue($router->hasRoute("post", "/"));
      }

      public function testClearUri()
      {
            $router = new Router();

            $this->assertSame("/post", $router->clearUri("/post/"));
      }

      public function getMockRequest()
      {
            $mock = $this->createMock(Request::class);
            $mock->method("run")->will;
      }
      public function testMethodRequest()
      {
            $router = new Router();

            $router->post("/", function (Request $req, Response $res) {
                  $res->json(["body" => $req->getBody()]);
            });

            $this->assertTrue($router->hasRoute("post", "/"));
      }
}
