<?php

use App\Http\Request;
use App\Http\Response;
use App\Routers\Router;

function userRouter(): Router
{
      $router = new Router();

      $router->before("/", function (Request $req, Response $res) {
            beforeHome($req, $res);
      });

      // init routes

      $router->get("/fetch/one/:id/:restaurandId", function (Request $req, Response $res) {
            HomeController($req, $res);
      });

      $router->get("/", function (Request $req, Response $res) {
            HomeController($req, $res);
      });


      $router->get("/all", function (Request $req, Response $res) {
            HomeController($req, $res);
      });




      $router->post("/", function (Request $req, Response $res) {
            $res->json(["body" => $req->getBody(), "Query" => $req->getQuery()]);
      });


      return $router;
}

function managerRouter(): Router
{
      $router = new Router();

      $router->before("/", function (Request $req, Response $res) {
            beforeHome($req, $res);
      });

      // init routes

      $router->get("/", function (Request $req, Response $res) {
            HomeController($req, $res);
      }, function (Request $req, Response $res) {
            $req->fillQuery();
      });


      $router->post("/", function (Request $req, Response $res) {
            $res->json(["body" => $req->getBody(), "Query" => $req->getQuery()]);
      });


      return $router;
}
