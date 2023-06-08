<?php
require(__DIR__ . "/../vendor/autoload.php");

use App\Http\Request;
use App\Http\Response;

$app = new App\App();

$app->accept("/", $app->json());


// $router = $app->router;

$app->get("/", function (Request $req, Response $res) {
      return $res->json($req->getHttpValues());
});

$app->post("/", function (Request $req, Response $res) {
      // var_dump($req);
      return $res->json($req->getHttpValues());
});

$app->run();




// var_dump($router->getRoutesMap());