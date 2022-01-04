<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use App\Controller\App;
use App\Controller\Router;
use App\Controller\Request;
use App\Controller\Response;
use App\Database\Migrations\Migration;
use App\Models\Color;

Router::get('/', function () {
    echo 'Frontify test Stefan!';
});

Router::get('/colors', function (Request $request, Response $response) {
    $response->toJSON(Color::find());
});

Router::post('/create', function (Request $request, Response $response) {
    $params = $request->getJSON();

    $color = new Color();

    $color->name = $params->name;
    $color->hexValue = $params->hexValue;

    $color->save();

    $response->toJSON($color);
});

Router::delete('/color/([0-9]*)', function(Request $request, Response $response) {
    $response->toJSON(Color::delete($request->parameters[0]));
});

Router::get('/migrate', function() {
    Migration::runMigration();
});

App::run();
