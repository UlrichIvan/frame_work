<?php

namespace App\tests\requests;

use App\Http\Request;
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
}
