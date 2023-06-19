<?php

namespace App\tests\requests;

use App\Http\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
      public function testRequestConstructor()
      {
            $request = new Request();

            $httpValues = (object) $request->getHttpValues();

            $this->assertNotEmpty($httpValues);
      }

      public function testSetAndQueryValue()
      {
            $request = new Request();

            $request->setQueryValue("type", "test");

            $this->assertSame("test", $request->getQueryValue("type"));
      }
}
