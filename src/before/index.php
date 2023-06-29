<?php

use App\Http\Request;
use App\Http\Response;

function beforeHome(Request $req, Response $res)
{
      if ($req->get("request", "method") === "POST") {
            $req->fillBody();
            $req->fillQuery();
      }

      if ($req->get("request", "method") === "GET") {
            $req->fillQuery();
      }
}
