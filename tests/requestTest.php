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
}
