<?php

use App\Http\Request;
use App\Http\Response;

function HomeController(Request $req, Response $res)
{
      $res->json($req->getQuery());
}
