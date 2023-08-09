<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/config.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('/json', function (Request $request, Response $response) {
    $data = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
    
    $response->getBody()->write($data);

    return $response->withHeader('Content-Type', 'text/plain');
});

$app->get('/text', function (Request $request, Response $response) {
    $data = 'Server funcionando.';
    
    $response->getBody()->write($data);

    return $response->withHeader('Content-Type', 'text/plain');
});
$app->get('/db', function (Request $request, Response $response) {
    // In this example, we'll return a simple string
    $data = '';

    require_once APP_ROOT . '/app/model/utilities/test.php';

    // Set the Content-Type header to plain text
    $response = $response->withHeader('Content-Type', 'text/plain');

    // Write the response body with the data
    $response->getBody()->write($data);

    return $response;
});

$app->post('/login', function(Request $request, Response $response)
    {
        require_once APP_ROOT . '/app/model/utilities/Login.php';
    }
);

$app->run();