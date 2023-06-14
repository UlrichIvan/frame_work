<?php

declare(strict_types=1);

namespace App\tests;

use App\App;
use Error;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
      public function testAppConstructor()
      {
            $app = new App();

            $this->assertNotEmpty($app->request);
            $this->assertNotEmpty($app->response);
      }

      public function testAcceptSucess()
      {

            $app = new App();

            $self = $app->accept("/", function () {
                  echo "global middleware";
            });

            $middlewares = $app->getMiddleWares("/");

            $this->assertCount(1, $middlewares);
            $this->assertInstanceOf(App::class, $self);
      }

      public function testAcceptWithException()
      {

            $app = new App();

            $this->expectException(Error::class);

            $app->accept("/", "callback");

            $middlewares = $app->getMiddleWares("/");

            $this->assertNull($middlewares);
      }

      public function testUrlencoded()
      {
            $app = new App();

            $cb = $app->urlencoded();

            $this->assertIsCallable($cb);
      }
}
