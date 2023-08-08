<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
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
$app->get('/', function (Request $request, Response $response)
{    
    $data = '';

    require_once APP_ROOT . '/app/model/utilities/test.php';
    
    $response = $response->withHeader('Content-Type', 'text/plain');
        
    $response->getBody()->write($data);
    
    return $response;
});

$app->get('/login', function (Request $request, Response $response)
{    
    $response->getBody()->write('TEST');
    
    return $response;
});

// Run App
$app->run();

?>