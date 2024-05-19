<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Database;
use App\UserApi;
use App\ProductApi;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/b-cart-api');
$database = new Database();

$app->post('/api/user/login', function (Request $request, Response $response) use ($database) {
    $userApi = new UserApi($database);
    $data = $request->getParsedBody();
    $user = isset($data['user'])? $data['user'] : '';
    $password = isset($data['password'])? $data['password'] : '';
    $result = $userApi->UserLogin($user, $password);
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/user/{id}', function (Request $request, Response $response, $args) use ($database) {
    $userApi = new UserApi($database);
    $id = isset($args['id'])? $args['id'] : '0';
    $result = $userApi->getUser($id);
    $response->getBody()->write(json_encode($result));
    return $response;
});



$app->run();
