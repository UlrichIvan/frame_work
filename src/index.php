<?php
require(__DIR__ . "/../vendor/autoload.php");

use App\Http\Request;
use App\Http\Response;

// create new Application 
$app = new App\App();

// add middleware that match request with 
// application/x-www-form-urlencoded 
// and fill the body and query if exists
$app->accept("/", $app->urlencoded());

$app->get("/", function (Request $req, Response $resp) {
      $resp->setStatus(500)->json(["status" => $resp->getStatus()]);
}, function (Request $req, Response $resp) {
      return $resp->json(["query" => $req->getQuery()]);
});

$app->post("/", function (Request $req, Response $resp) {
      $resp->setStatus(200)->json(["body" => $req->getBody()])->close();
}, function (Request $req, Response $resp) {
      $resp->json(["body" => $req->getBody()])->close();
});

$app->post("/articles", function (Request $req, Response $resp) {
      $resp->setStatus(200)->json([$req->getBody()])->close();
}, function (Request $req, Response $resp) {
      $resp->json(["body" => $req->getBody()])->close();
});

$app->run();




// var_dump($router->getRoutesMap());