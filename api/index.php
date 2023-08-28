<?php
/////////////////////////////////////////////////////////////
#region - - - INIT - - -
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

// CORS
//require __DIR__ . '/config/cors.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use App\Model\Classes\Account;
use App\Model\Classes\DataAccess;
use App\Model\Classes\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Model\Utilities\Log;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/config.php';
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
$app->group('/test', function ($group) {
    $group->get('/json', function (Request $request, Response $response) {
        $data = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/text', function (Request $request, Response $response) {
        $data = 'Server funcionando.';
        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'text/plain');
    });

    $group->get('/db', function (Request $request, Response $response) {
        $data = '';
        require_once APP_ROOT . '/app/model/utilities/test.php';
        $response = $response->withHeader('Content-Type', 'text/plain');
        $response->getBody()->write($data);
        return $response;
    });
});
#endregion

/////////////////////////////////////////////////////////////
#region - - - ROUTES - - -
$app->group('/auth', function ($group) {
    $group->get('/validate', function(Request $request, Response $response) {
        $account = new Account();                
        if($account->ValidateSession(DB_SQLITE_PATH)) {
            $dataAccess = new DataAccess();
            $session = new Session();

            $sessionID = $session->getSessionFromCookie();
            $playerID = $session->findSessionPlayer($sessionID, DB_SQLITE_PATH);
            $user = $dataAccess->GetSingleColumn('users', 'name', ['id'], [$playerID], DB_SQLITE_PATH);
            $responseData = ['user' => $user];

            $response->getBody()->write(json_encode($responseData));            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(401);
        }
    });

    $group->post('/login', function(Request $request, Response $response) {
        try
        {
            $account = new Account();
            if($account->Login(DB_SQLITE_PATH)) {
                $data = json_decode(file_get_contents('php://input'), true);                
                $responseData = ['user' => $data['user']];
                
                $response->getBody()->write(json_encode($responseData));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            } else {
                return $response->withStatus(400);
            }
        }
        catch(Exception $e)
        {
            Log::WriteLog('error.txt', $e->getMessage());
        }
    });

    $group->post('/logout', function(Request $request, Response $response) {
        $account = new Account();
        if($account->Logout(DB_SQLITE_PATH)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(400);
        }
    });
});
#endregion

$app->run();