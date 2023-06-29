<?php

namespace App\interface;

interface ResponseInterface
{
      // list of http code availables
      const HTTP_CODE_RESPONSES = [

            // informations responses
            100,
            101,
            103,

            // success responses
            200,
            201,
            202,
            203,
            204,
            205,
            206,
            207,
            208,
            226,

            // redirections messages
            300,
            301,
            302,
            303,
            304,
            307,
            308,
            // clients errors responses
            400,
            401,
            402,
            403,
            404,
            405,
            406,
            407,
            408,
            409,
            410,
            411,
            412,
            413,
            414,
            415,
            416,
            417,
            418,
            421,
            422,
            423,
            424,
            425,
            426,
            427,
            428,
            429,
            430,
            431,
            451,
            // errors server responses
            500,
            501,
            502,
            503,
            504,
            505,
            506,
            507,
            508,
            509,
            510,
            5011,
      ];
}
