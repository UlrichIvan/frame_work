<?php

namespace App\tests\requests;

use App\Http\Request;
use App\Http\Response;
use App\Routers\Router;
use Exception;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
      public function testRequestConstructor()
      {
            $request = new Request();

            $httpValues = $request->getHttpValues();

            $this->assertNotEmpty($httpValues);
            $this->assertIsArray($httpValues);
      }


      public function testSetAndQueryValue()
      {
            $request = new Request();

            $request->setQueryValue("type", "test");

            $this->assertSame("test", $request->getQueryValue("type"));
      }


      public function testAddAndGetPropertyValue()
      {
            $request = new Request();

            $request->add("name", "test");

            $this->assertSame("test", $request->getPropertyValue("name"));
      }

      public function testHasParamsNames()
      {
            $request = new Request();

            $this->assertTrue($request->hasParamsNames("fetch/one/:id"));
      }

      public function testGetParamsNamesIntoUri()
      {
            $request = new Request();

            $params = $request->getParamsNamesIntoUri("fetch/one/:id");

            $this->assertNotEmpty($params);
            $this->assertIsArray($params);
            $this->assertSame(["id"], array_values($params));
            $this->assertSame([2], array_keys($params));
      }

      public function testSetParams()
      {
            $request = new Request();

            $request->setParams("fetch/one/1", "fetch/one/:id", [2 => "id"]);

            $params = $request->getParams();

            $this->assertNotEmpty($params);
            $this->assertIsArray($params);
            $this->assertSame(["id" => '1'], $params);
            $this->assertSame("fetch/one/1", $request->getCurrentUri());
      }

      public function testGetServerValueFromRequestObject()
      {
            $request = new Request();

            $this->assertNull($request->getHttpValue("content_type"));
      }

      public function testGetServerValueWithUnexistingPropertyFromRequetObject()
      {
            $request = new Request();


            $this->expectException(Exception::class);

            $request->getNotExistValue("content_type");
      }


      public function testGetQueryValueAndSetQueryValue()
      {
            $request = new Request();


            $request->setQueryValue("name", "michel");

            $this->assertSame("michel", $request->getQueryValue("name"));
      }

      public function testGetQueryValues()
      {
            $request = new Request();


            $request->setQueryValue("name", "michel");

            $request->setQueryValue("id", 1);

            $this->assertCount(2, $request->getQuery());
      }

      public function getMockRequest()
      {
            return $this->getMockBuilder(Request::class)
                  ->onlyMethods(["hasParamsNames", "getUri", "fillQuery", "fillBody"])
                  ->addMethods(["getRequestValue"])->getMock();
      }

      public function testFillQueryAndFillBody()
      {
            // set up mock object
            $mockRequest = $this->getMockRequest();


            // configuration of method mocked
            $mockRequest->method("hasParamsNames")->willReturn(false);
            $mockRequest->method("getUri")->willReturn("/");
            $mockRequest->method("getRequestValue")->willReturn("post");
            $mockRequest->method("fillQuery")->willReturn(new Request());
            $mockRequest->method("fillBody")->willReturn(new Request());


            // mock excepts declaration
            $mockRequest->expects($this->once())->method("fillQuery");

            $router = new Router($mockRequest);


            // actions who will trigger call mocked functions
            $this->expectOutputString("called");

            $router->post(
                  "/",
                  function () {
                        echo "called";
                  },
                  function ($req, $res) {
                        $req->fillBody();
                        $req->fillQuery();
                  },
            )->readyGo();
      }


      public function testSetBodyValue()
      {
            $request = new Request();

            $request->setBodyValue("name", "Michel");

            $this->assertCount(1, $request->getBody());
      }

      public function testGetUri()
      {
            $request = new Request();

            $this->assertNull($request->getUri());
      }


      public function testGetBodyValue()
      {
            $request = new Request();

            $request->setBodyValue("name", "Michel");

            $this->assertSame("Michel", $request->getBodyValue("name"));
      }
}
