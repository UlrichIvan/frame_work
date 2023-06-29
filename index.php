<?php
require(__DIR__ . "/../vendor/autoload.php");

use App\App;
use App\Router\Router;

App::use("/api/users", function (): Router {
      return userRouter();
});


App::use("/api/managers", function (): Router {
      return managerRouter();
});


App::run();
