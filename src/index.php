<?php

use App\Http\Request;
use App\Http\Response;
use App\Router\Router;

require(__DIR__ . "/../vendor/autoload.php");

$router = new Router();

// init accepts values

// $router->accept("/", function (Request $req, Response $res) {
//       if ($req->get("request", "method") === "POST") {
//             $req->fillBody();
//       }

//       if ($req->get("request", "method") === "GET") {
//             $req->fillQuery();
//       }
// });

// init routes

$router->get("/", function (Request $req, Response $res) {
      $res->json($req->getQuery());
}, function (Request $req, Response $res) {
      $req->fillQuery();
});

$router->post("/", function (Request $req, Response $res) {
      $res->json($req->getBody());
}, function (Request $req, Response $res) {
      $req->fillBody();
});


$router->ready();
