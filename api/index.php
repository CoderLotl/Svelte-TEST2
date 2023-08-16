<?php
/////////////////////////////////////////////////////////////
#region - - - INIT - - -
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use App\Model\Classes\Account;
use App\Model\Classes\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

use function App\Model\Utilities\Login\Login;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/config.php';
#endregion

/////////////////////////////////////////////////////////////
#region - - - SESSION - - -
// Updating both the session and the cookie session to extend the session's lifetime.
$session = Session::getSessionFromCookie();

if($session && Session::findSessionInDatabase($session, DB_SQLITE_PATH))
{
    Session::updateSessionCookie($session);
    Session::updateSessionInDatabase($session, DB_SQLITE_PATH);
}
#endregion

/////////////////////////////////////////////////////////////
#region - - - SERVER - - -
// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();
#endregion

/////////////////////////////////////////////////////////////
#region - - - TEST ROUTES - - -
$app->get('/json', function (Request $request, Response $response) {
    $data = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
    
    $response->getBody()->write($data);

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/text', function (Request $request, Response $response) {
    $data = 'Server funcionando.';
    
    $response->getBody()->write($data);

    return $response->withHeader('Content-Type', 'text/plain');
});

$app->get('/db', function (Request $request, Response $response) {    
    $data = '';

    require_once APP_ROOT . '/app/model/utilities/test.php';
    
    $response = $response->withHeader('Content-Type', 'text/plain');
    
    $response->getBody()->write($data);

    return $response;
});
#endregion

/////////////////////////////////////////////////////////////
#region - - - ROUTES - - -
$app->get('/validate', function(Request $request, Response $response)
    {
        if(Account::ValidateSession(DB_SQLITE_PATH))
        {
            return $response->withStatus(200);
        }
        else
        {
            return $response->withStatus(401);
        }
    }
);

$app->post('/login', function(Request $request, Response $response)
    {           
        if(Account::Login(DB_SQLITE_PATH))
        {
            return $response->withStatus(200);
        }
        else
        {
            return $response->withStatus(400);
        }
    }
);
#endregion

$app->run();