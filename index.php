<?php
require_once(__DIR__ . "/vendor/autoload.php");

use App\App;
use App\Routers\Router;

$app = new App();

$app->use("/api/users", function (): Router {
      return  userRouter();
});


$app->use("/api/managers", function (): Router {
      return managerRouter();
});


$app->run();
