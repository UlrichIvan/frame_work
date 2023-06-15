<?php

declare(strict_types=1);

namespace App\tests;

use App\Router\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
      public function testConstructor()
      {
            $router = new Router();
            $this->assertNotEmpty($router->request);
            $this->assertNotEmpty($router->response);
      }

      public function testSetMiddlewareToRoute()
      {
            $router = new Router();
            // $router->setMiddlewareToRoute("/",1);
      }
}
