<?php

namespace App\tests\routers;

use App\Http\Request;
use App\Http\Response;
use App\Routers\Router;
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

      public function testRouteWithoutMiddlewares()
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

      public function testRouteWithCallbackAndMiddleware()
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


      public function testRouteWithArrayMiddlewares()
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

      public function testAddGlobalArrayMiddleware()
      {
            $router = new Router();

            // add middlewares
            $router->before("/", [function () {
                  // do something...
            }, function () {
                  // do something...
            }]);


            $this->assertNotEmpty($router->getBefores());
            $this->assertCount(2, $router->getBefore("/"));
            $this->assertIsCallable($router->getBefore("/")[0]);
            $this->assertIsCallable($router->getBefore("/")[1]);
      }

      public function testAddGlobalCallbackMiddleware()
      {
            $router = new Router();

            // add middlewares
            $router->before("/", function () {
                  // do something...
            });


            $this->assertNotEmpty($router->getBefores());
            $this->assertCount(1, $router->getBefore("/"));
            $this->assertIsCallable($router->getBefore("/")[0]);
      }

      public function testAddCallbackMiddlewareToRoute()
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

            $this->assertInstanceOf(Route::class, $route);
            $this->assertCount(0, $router->getBefore("/"));
            $this->assertCount(1, $route->getMiddlewares());
            $this->assertIsCallable($route->getMiddlewares()[0]);
      }

      public function testAddArrayCallbackMiddlewareToRoute()
      {
            $router = new Router();

            $router->post(
                  "/",
                  function (Request $req, Response $res) {
                        $res->json(["body" => $req->getBody()]);
                  },
                  [function () {
                  }]
            );

            $route = $router->getRoute("post", "/");

            $this->assertInstanceOf(Route::class, $route);
            $this->assertCount(0, $router->getBefore("/"));
            $this->assertCount(1, $route->getMiddlewares());
            $this->assertIsCallable($route->getMiddlewares()[0]);
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

      public function testGetRouteWithoutParamsSuccess()
      {
            $router = new Router();

            $router->post(
                  "/",
                  function () {
                        //     code here...
                  }
            );

            $this->assertInstanceOf(
                  Route::class,
                  $router->getRoute("post", "/")
            );
      }


      public function testGetRouteWithParamsSuccess()
      {
            $router = new Router();

            $router->post(
                  "/users/:id",
                  function () {
                        //     code here...
                  }
            );

            $route = $router->getRoute("post", "/users/:id");

            $this->assertInstanceOf(
                  Route::class,
                  $route
            );

            $this->assertSame(
                  "/users/:id",
                  $route->getUri()
            );

            $this->assertSame(
                  [2 => "id"],
                  $route->getParamsNames()
            );
      }

      public function testGetRouteWithoutParamsLost()
      {
            $router = new Router();

            $this->assertNull(
                  $router->getRoute("post", "/")
            );
      }

      public function getMockRequest($onlyMethods)
      {
            // set up mock object
            $mockRequest = $this->getMockBuilder(Request::class)->onlyMethods($onlyMethods)->addMethods(["getRequestValue"])->getMock();

            return $mockRequest;
      }

      public function getMockResponse($onlyMethods)
      {
            // set up mock object
            $mockResponse = $this->getMockBuilder(Response::class)->onlyMethods($onlyMethods)->getMock();

            return $mockResponse;
      }


      public function testMethodRequestWithOutMiddlewaresSuccess()
      {
            // set up mock object
            $mockRequest = $this->getMockRequest(["hasParamsNames", "getUri"]);

            // configuration of method mocked
            $mockRequest->method("hasParamsNames")->willReturn(false);
            $mockRequest->method("getUri")->willReturn("/");
            $mockRequest->method("getRequestValue")->willReturn("post");



            // expects assertions
            $mockRequest->expects($this->once())->method("hasParamsNames")->with('/');

            $router = new Router($mockRequest);


            // actions who will trigger call mocked functions
            $this->expectOutputString("called");

            $router->post(
                  "/",
                  function () {
                        echo "called";
                  }
            )->readyGo();
      }

      // public function testMethodRequestWithOutMiddlewaresFailled()
      // {
      //       $mockRequest = $this->getMockRequest(["hasParamsNames", "getUri"]);
      //       $mockResponse = $this->getMockResponse(["close", "status"]);


      //       // configuration of method mocked
      //       $mockRequest->method("hasParamsNames")->willReturn(false);
      //       $mockRequest->method("getUri")->willReturn("/");
      //       $mockRequest->method("getRequestValue")->willReturn("get");
      //       $mockResponse->method("status")->willReturn(new Response());



      //       // expects assertions request 
      //       $mockRequest->expects($this->once())->method("hasParamsNames")->with('/');

      //       // expects assertions response 
      //       $mockResponse->expects($this->once())->method("status")->with(404);

      //       // implementation of code 
      //       $router = new Router($mockRequest, $mockResponse);


      //       $router->post(
      //             "/",
      //             function () {
      //                   echo "called";
      //             }
      //       )->readyGo();
      // }

      public function testMethodRequestWithMiddlewaresSuccess()
      {

            $mockRequest = $this->getMockRequest(["hasParamsNames", "getUri", "fillBody"]);

            // // configuration of method mocked
            $mockRequest->method("hasParamsNames")->willReturn(false);
            $mockRequest->method("getUri")->willReturn("/");
            $mockRequest->method("getRequestValue")->willReturn("post");
            $mockRequest->method("fillBody")->willReturn(new Request());


            // expects assertions
            $mockRequest->expects($this->once())->method("hasParamsNames")->with('/');
            $mockRequest->expects($this->once())->method("fillBody");

            $this->expectOutputString("called");


            // actions who will trigger call mocked functions
            $router = new Router($mockRequest);

            $router->post(
                  "/",
                  function () {
                        echo "called";
                  },
                  function (Request $req, Response $res) {
                        $req->fillBody();
                  }
            )->readyGo();
      }
}
