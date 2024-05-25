<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use App\Database;
use App\UserApi;
use App\ProductApi;
use App\VendorApi;
use App\CartApi;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/bcart-api');
$database = new Database();

$corsMiddleware = function (Request $request, RequestHandlerInterface $handler) : Response {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
};

$app->add($corsMiddleware);

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

$app->get('/api/users', function (Request $request, Response $response, $args) use ($database) {
    $userApi = new UserApi($database);
    $result = $userApi->getUsers();
    $response->getBody()->write(json_encode($result));
    return $response;
});


$app->post('/api/product/bulkadd', function (Request $request, Response $response, $args) use ($database) {
    $productApi = new ProductApi($database);
    $data = $request->getParsedBody();
    $result = $productApi->bulkaddProduct($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/product/delete', function (Request $request, Response $response, $args) use ($database) {
    $productApi = new ProductApi($database);
    $data = $request->getParsedBody();
    $productid = isset($data['productid'])? $data['productid'] : '0';
    $result = $productApi->deleteProduct($productid);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/product/update', function (Request $request, Response $response, $args) use ($database) {
    $productApi = new ProductApi($database);
    $data = $request->getParsedBody();
    $result = $productApi->updateProduct($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/cart/add', function (Request $request, Response $response, $args) use ($database) {
    $CartApi = new CartApi($database);
    $data = $request->getParsedBody();
    $result = $CartApi->insertCartItem($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/cart/update', function (Request $request, Response $response, $args) use ($database) {
    $CartApi = new CartApi($database);
    $data = $request->getParsedBody();
    $result = $CartApi->updateCartItem($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/cart/delete', function (Request $request, Response $response, $args) use ($database) {
    $CartApi = new CartApi($database);
    $data = $request->getParsedBody();
    $result = $CartApi->deleteCartItem($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->run();
