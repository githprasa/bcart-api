<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Database;
use App\UserApi;
use App\ProductApi;
use App\VendorApi;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/bcart-api');
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

/*product*/
$app->get('/api/products/getlist', function (Request $request, Response $response, $args) use ($database) {
    $productApi = new ProductApi($database);
    $result = $productApi->productResult();
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/products/addProduct', function (Request $request, Response $response) use ($database) {
    $productApi = new ProductApi($database);
    $data = $request->getParsedBody();
    $result = $productApi->addProduct($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*vendor*/
$app->get('/api/vendor/getlist', function (Request $request, Response $response, $args) use ($database) {
    $productApi = new VendorApi($database);
    $result = $productApi->getVendorList();
    $response->getBody()->write(json_encode($result));
    return $response;
});
$app->run();
