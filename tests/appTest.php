<?php

use App\App;
use App\Http\Request;
use App\Http\Response;
use App\Routers\Router;
use PHPUnit\Framework\TestCase;

final class appTest extends TestCase
{
      public function testConstructor()
      {
            $app = new App();

            $this->assertInstanceOf(Request::class, $app->request);
            $this->assertInstanceOf(Response::class, $app->response);

            $this->assertNotEmpty($app->request);
            $this->assertNotEmpty($app->response);
      }

      public function testUse()
      {
            $app = new App();

            $app->use("/", function (): Router {
                  // implementation here...
                  return new Router();
            });

            $routers = $app->getRouters();

            $this->assertTrue($routers->has("/"));

            $this->assertInstanceOf(Router::class, $routers->get("/"));
      }

      public function testBeforeIt()
      {
            $app = new App();

            $app->beforeIt("/");

            $this->assertSame("/", $app->getCurrentPrefix());
      }


      public function testDoWithPrefixAndCallback()
      {
            $app = new App();

            $app->do(function () {
                  echo "Hello";
            }, "/");

            $this->assertSame("/", $app->getCurrentPrefix());
            $this->assertTrue($app->getBeforeIts()->has("/"));
            $this->assertIsCallable($app->getBeforeIts()->get("/")[0]);
      }

      public function testDoWithBeforeItWithCallback()
      {
            $app = new App();

            $app->beforeIt("/")->do(function () {
                  echo "Hello";
            });

            $this->assertSame("/", $app->getCurrentPrefix());
            $this->assertTrue($app->getBeforeIts()->has("/"));
            $this->assertIsCallable($app->getBeforeIts()->get("/")[0]);
      }

      public function testDoWithPrefixAndArrayCallback()
      {
            $app = new App();

            $app->do([function () {
                  echo "Hello";
            }, function () {
                  echo "Hello";
            }], "/");

            $this->assertSame("/", $app->getCurrentPrefix());
            $this->assertTrue($app->getBeforeIts()->has("/"));
            $this->assertCount(2, $app->getBeforeIts()->get("/"));


            $this->assertIsCallable($app->getBeforeIts()->get("/")[0]);
            $this->assertIsCallable($app->getBeforeIts()->get("/")[1]);
      }

      public function testDoWithPrefixAndCallbackAndException()
      {
            $app = new App();

            $this->expectException(InvalidArgumentException::class);

            $app->do(function () {
                  echo "Hello";
            });
      }

      public function testSetRouters()
      {
            $app = new App();

            $routers = $app->getRouters();

            $routers->add("/", new Router());

            $app->setRouters($routers);

            $this->assertCount(1, $app->getRouters());
            $this->assertTrue($app->getRouters()->has("/"));
            $this->assertInstanceOf(Router::class, $app->getRouters()->get("/"));
      }

      public function testRunMethod()
      {
            $request = $this->getMockBuilder(Request::class)
                  ->onlyMethods(["hasParamsNames", "clearUri", "getUri", "fillQuery", "fillBody"])
                  ->addMethods(["getRequestValue"])->getMock();

            // custom methods

            $request->method('getUri')->willReturn("/");

            // expect declarations

            $request->expects($this->once())->method("getUri");


            // router

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

            $app = new App($request);

            $app->use("/api/users", function (): Router {
                  return new Router();
            });


            $routers = $app->getRouters();

            $routers->add("/", new Router());

            $app->setRouters($routers);

            $this->assertCount(1, $app->getRouters());
            $this->assertTrue($app->getRouters()->has("/"));
            $this->assertInstanceOf(Router::class, $app->getRouters()->get("/"));
      }
}
