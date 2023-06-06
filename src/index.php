<?php

use App\Router\Router;

require(__DIR__ . "/../vendor/autoload.php");

$router = new Router();

$router->get("/", function () {
      echo ("hello world!!");
});

$router->post("/post/", function () {
      echo ("hello world!!");
});

var_dump($router->getRoutesMap());
