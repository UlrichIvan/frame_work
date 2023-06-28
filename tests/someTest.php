<?php

use App\Router\Router;
use PHPUnit\Framework\TestCase;

class someTest extends TestCase
{
      public function testOne()
      {

            $mock = $this->createMock(Router::class);

            $mock->method("ready")->willReturn("ok");

            $this->assertSame("ok", $mock->ready());
      }
}
