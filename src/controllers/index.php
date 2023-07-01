<?php

use App\Http\Request;
use App\Http\Response;

function HomeController(Request $req, Response $res)
{
      $req->fillQuery();
      $res->json($req->getQuery());
}
