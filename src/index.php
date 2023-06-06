<?php

use App\Http\Request;
use App\Http\Response;
use App\Router\Router;

require(__DIR__ . "/../vendor/autoload.php");

$router = new Router();

$router->get("/", function (Request $req, Response $resp) {
      return $resp->json([
            "name" => "michel",
            "status" => 200
      ]);
});

$router->post("/post/", function (Request $req, Response $resp) {
      return $resp->json([
            "name" => "michel",
            "status" => 200
      ]);
});

$router->matchRequest();



// var_dump($router->getRoutesMap());
