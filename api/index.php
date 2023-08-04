<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: GET,POST');
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
//load autoloader
require_once './vendor/autoload.php';
//load config
require_once './config/config.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Create App
$app = AppFactory::create();

// Define routes
$app->get('/', function (Request $request, Response $response) {
    // In this example, we'll return a simple string
    $data = '';

    require_once APP_ROOT . '/app/model/utilities/test.php';

    // Set the Content-Type header to plain text
    $response = $response->withHeader('Content-Type', 'text/plain');
    
    // Write the response body with the data
    $response->getBody()->write($data);
    
    return $response;
});

// Run App
$app->run();

?>