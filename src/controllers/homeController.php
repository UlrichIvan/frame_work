<?php

use App\Http\Request;
use App\Http\Response;

function HomeController(Request $req, Response $res)
{
      $req->fillQuery();

      $res->status(200)->header("Content-Type:", "application/json; charset=utf-8")->json(
            [
                  "query" => $req->getQuery(),
                  "params" => $req->getParams(),
            ]
      );
}
