<?php

use App\Http\Request;
use App\Http\Response;

function HomeController(Request $req, Response $res)
{
      $req->fillQuery();

      $res->setStatus(200)->setHeader("Content-Type:", "application/json; charset=utf-8")->json($req->getQuery());
}
