<?php
require_once(__DIR__ . "/vendor/autoload.php");

use App\App;
use App\Http\Request;
use App\Http\Response;
use App\Routers\Router;

$app = new App();

// add action before execute the any action associate to prefix uri
$app->beforeIt("/api/users")->do([function (Request $req, Response $res) {
      echo "beforeIt\n";
}]);

// add each router to his prefix uri

$app->use("/api/users", function (): Router {
      return  userRouter();
});


$app->use("/api/managers", function (): Router {
      return managerRouter();
});


$app->run();
