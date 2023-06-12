<?php
require(__DIR__ . "/../vendor/autoload.php");

use App\Http\Request;
use App\Http\Response;

// create new Application 
$app = new App\App();

// add middleware that match request with application/x-www-form-urlencoded and retrive the body values
$app->accept("/", $app->urlencoded());

$app->get("/", function (Request $req, Response $resp) {
      return $resp->json(["query" => $req->getQuery()]);
});

$app->post("/", function (Request $req, Response $resp) {

      $resp->json(["body" => $req->getBody()]);

      // $string = array_keys($_POST)[0];
      // $body = preg_replace("#(\r\n|\r|\n|_)#", "", $string);
      // foreach ($_POST as $key => $value) {
      //       $val = json_decode($key);
      //       $resp->json($val);
      // }
      // var_dump($body);
      // return $resp->json([json_decode($body), $string]);
});

$app->run();




// var_dump($router->getRoutesMap());