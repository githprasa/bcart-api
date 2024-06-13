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
use App\OrdersApi;
use App\CategoryApi;
use App\LocationApi;
use App\SettingsApi;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/bcart-api');
$database = new Database();

$corsMiddleware = function (Request $request, RequestHandlerInterface $handler) : Response {
    if ($request->getMethod() == 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
    } else {
        $response = $handler->handle($request);
    }
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    // if ($request->getMethod() == 'OPTIONS') {
    //     return $response;
    // }
    return $response;
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

/*user*/
$app->get('/api/user/{id}', function (Request $request, Response $response, $args) use ($database) {
    $userApi = new UserApi($database);
    $id = isset($args['id'])? $args['id'] : '0';
    $result = $userApi->getUser($id);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->get('/api/users', function (Request $request, Response $response, $args) use ($database) {
    $userApi = new UserApi($database);
    $result = $userApi->getUsers();
    $response->getBody()->write(json_encode($result));
    return $response;
});


/*Add new user*/
$app->post('/api/user/addUser', function (Request $request, Response $response) use ($database) {
    $userApi = new UserApi($database);
    $data = $request->getParsedBody();
    $params = [];
    $params['FirstName'] = isset($data['FirstName'])? $data['FirstName'] : '';
    $params['LastName'] = isset($data['LastName'])? $data['LastName'] : '';
    $params['RoleId'] = isset($data['RoleId'])? $data['RoleId'] : '';
    $params['Email'] = isset($data['Email'])? $data['Email'] : '';
    $params['Phone'] = isset($data['Phone'])? $data['Phone'] : '';
    $params['Address1'] = isset($data['Address1'])? $data['Address1'] : '';
    $params['Address2'] = isset($data['Address2'])? $data['Address2'] : '';
    $params['UserId'] = isset($data['UserId'])? $data['UserId'] : '';
    $params['Password'] = isset($data['Password'])? $data['Password'] : '';
    $params['LocationId'] = isset($data['LocationId'])? $data['LocationId'] : '';
    $params['CostObject'] = isset($data['CostObject'])? $data['CostObject'] : '';
    $params['Currency'] = isset($data['Currency'])? $data['Currency'] : '';
    $result = $userApi->addUser($params);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Update user*/
$app->post('/api/user/updateUser', function (Request $request, Response $response) use ($database) {
    $userApi = new UserApi($database);
    $data = $request->getParsedBody();
    $params = [];
    $params['FirstName'] = isset($data['FirstName'])? $data['FirstName'] : '';
    $params['LastName'] = isset($data['LastName'])? $data['LastName'] : '';
    $params['RoleId'] = isset($data['RoleId'])? $data['RoleId'] : '';
    $params['Email'] = isset($data['Email'])? $data['Email'] : '';
    $params['Phone'] = isset($data['Phone'])? $data['Phone'] : '';
    $params['Address1'] = isset($data['Address1'])? $data['Address1'] : '';
    $params['Address2'] = isset($data['Address2'])? $data['Address2'] : '';
    $params['UserId'] = isset($data['UserId'])? $data['UserId'] : '';
    $params['Password'] = isset($data['Password'])? $data['Password'] : '';
    $params['LocationId'] = isset($data['LocationId'])? $data['LocationId'] : '';
    $params['CostObject'] = isset($data['CostObject'])? $data['CostObject'] : '';
    $params['Currency'] = isset($data['Currency'])? $data['Currency'] : '';
    $params['id'] = isset($data['id'])? $data['id'] : 0;    
    $result = $userApi->updateUser($params);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Delete user*/
$app->post('/api/user/deleteUser', function (Request $request, Response $response) use ($database) {
    $userApi = new UserApi($database);
    $data = $request->getParsedBody();
    $userid = isset($data['id'])? $data['id'] : '';
    $result = $userApi->deleteUser($userid);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*product*/
/*get full product list*/
$app->get('/api/products/getlist', function (Request $request, Response $response, $args) use ($database) {
    $productApi = new ProductApi($database);
    $result = $productApi->productResult();
    $response->getBody()->write(json_encode($result));
    return $response;
});
/*get product details*/
$app->get('/api/products/getProductdetails/{id}', function (Request $request, Response $response, $args) use ($database) {
    $productApi = new ProductApi($database);
    $id = isset($args['id'])? $args['id'] : 0;
    $result = $productApi->getProductdetails($id);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Add new product*/
$app->post('/api/products/addProduct', function (Request $request, Response $response) use ($database) {
    $productApi = new ProductApi($database);
    $data = $request->getParsedBody();
    $result = $productApi->addProduct($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Update product
$app->post('/api/products/updateProduct', function (Request $request, Response $response) use ($database) {
    $productApi = new ProductApi($database);
    $data = $request->getParsedBody();
    $result = $productApi->updateProduct($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Delete product
$app->post('/api/products/deleteProduct', function (Request $request, Response $response) use ($database) {
    $productApi = new ProductApi($database);
    $data = $request->getParsedBody();
    $userid = isset($data['id'])? $data['id'] : 0;
    $result = $productApi->deleteProduct($userid);
    $response->getBody()->write(json_encode($result));
    return $response;
});*/

/*vendor get full list*/
$app->get('/api/vendor/getlist', function (Request $request, Response $response, $args) use ($database) {
    $vendorApi = new VendorApi($database);
    $result = $vendorApi->getVendorList();
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*get vendor details*/
$app->get('/api/vendor/getVendordetails/{id}', function (Request $request, Response $response, $args) use ($database) {
    $vendorApi = new VendorApi($database);
    $id = isset($args['id'])? $args['id'] : '0';
    $result = $vendorApi->getVendordetails($id);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Add new vendor*/
$app->post('/api/vendor/addVendor', function (Request $request, Response $response) use ($database) {
    $vendorApi = new VendorApi($database);
    $data = $request->getParsedBody();
    $vendorparams = [];
    $vendorparams['vendor'] = isset($data['vendor'])? $data['vendor'] : '';
    $vendorparams['description'] = isset($data['description'])? $data['description'] : '';
    $vendorparams['location'] = isset($data['location'])? $data['location'] : '';
    $vendorparams['erp_ref'] = isset($data['erp_ref'])? $data['erp_ref'] : '';
    $result = $vendorApi->addVendor($vendorparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Update vendor*/
$app->post('/api/vendor/updateVendor', function (Request $request, Response $response) use ($database) {
    $vendorApi = new VendorApi($database);
    $vendorparams = [];
    $data = $request->getParsedBody();
    $vendorparams['vendor'] = isset($data['vendor'])? $data['vendor'] : '';
    $vendorparams['description'] = isset($data['description'])? $data['description'] : '';
    $vendorparams['location'] = isset($data['location'])? $data['location'] : '';
    $vendorparams['erp_ref'] = isset($data['erp_ref'])? $data['erp_ref'] : '';
    $vendorparams['id'] = isset($data['id'])? $data['id'] : 0;    
    $result = $vendorApi->updateVendor($vendorparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Delete vendor*/
$app->post('/api/vendor/deleteVendor', function (Request $request, Response $response) use ($database) {
    $vendorApi = new VendorApi($database);
    $data = $request->getParsedBody();
    $userid = isset($data['id'])? $data['id'] : '';
    $result = $vendorApi->deleteVendor($userid);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Category get full list*/
$app->get('/api/category/getlist', function (Request $request, Response $response, $args) use ($database) {
    $categoryApi = new CategoryApi($database);
    $result = $categoryApi->getCategoryList();
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*get category details*/
$app->get('/api/category/getCategorydetails/{id}', function (Request $request, Response $response, $args) use ($database) {
    $categoryApi = new CategoryApi($database);
    $id = isset($args['id'])? $args['id'] : '0';
    $result = $categoryApi->getCategorydetails($id);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Add new category*/
$app->post('/api/category/addCategory', function (Request $request, Response $response) use ($database) {
    $categoryApi = new CategoryApi($database);
    $data = $request->getParsedBody();
    $categoryparams = [];
    $categoryparams['category'] = isset($data['category'])? $data['category'] : '';
    $categoryparams['description'] = isset($data['description'])? $data['description'] : '';
    $result = $categoryApi->addCategory($categoryparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Update category*/
$app->post('/api/category/updateCategory', function (Request $request, Response $response) use ($database) {
    $categoryApi = new CategoryApi($database);
    $categoryparams = [];
    $data = $request->getParsedBody();
    $categoryparams['category'] = isset($data['category'])? $data['category'] : '';
    $categoryparams['description'] = isset($data['description'])? $data['description'] : '';
    $categoryparams['id'] = isset($data['id'])? $data['id'] : 0;    
    $result = $categoryApi->updateCategory($categoryparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Delete category*/
$app->post('/api/category/deleteCategory', function (Request $request, Response $response) use ($database) {
    $categoryApi = new CategoryApi($database);
    $data = $request->getParsedBody();
    $userid = isset($data['id'])? $data['id'] : '';
    $result = $categoryApi->deleteCategory($userid);
    $response->getBody()->write(json_encode($result));
    return $response;
});


/*Location get full list*/
$app->get('/api/location/getlist', function (Request $request, Response $response, $args) use ($database) {
    $locationApi = new LocationApi($database);
    $result = $locationApi->getLocationList();
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*get location details*/
$app->get('/api/location/getLocationdetails/{id}', function (Request $request, Response $response, $args) use ($database) {
    $locationApi = new LocationApi($database);
    $id = isset($args['id'])? $args['id'] : '0';
    $result = $locationApi->getLocationdetails($id);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Add new location*/
$app->post('/api/location/addLocation', function (Request $request, Response $response) use ($database) {
    $locationApi = new LocationApi($database);
    $data = $request->getParsedBody();
    $locationparams = [];
    $locationparams['location'] = isset($data['location'])? $data['location'] : '';
    $locationparams['area'] = isset($data['area'])? $data['area'] : '';
    $locationparams['city'] = isset($data['city'])? $data['city'] : '';
    $locationparams['state'] = isset($data['state'])? $data['state'] : '';
    $locationparams['country'] = isset($data['country'])? $data['country'] : '';
    $locationparams['zip'] = isset($data['zip'])? $data['zip'] : '';
    $result = $locationApi->addLocation($locationparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Update location*/
$app->post('/api/location/updateLocation', function (Request $request, Response $response) use ($database) {
    $locationApi = new LocationApi($database);
    $locationparams = [];
    $data = $request->getParsedBody();
    $locationparams['location'] = isset($data['location'])? $data['location'] : '';
    $locationparams['area'] = isset($data['area'])? $data['area'] : '';
    $locationparams['city'] = isset($data['city'])? $data['city'] : '';
    $locationparams['state'] = isset($data['state'])? $data['state'] : '';
    $locationparams['country'] = isset($data['country'])? $data['country'] : '';
    $locationparams['zip'] = isset($data['zip'])? $data['zip'] : '';
    $locationparams['id'] = isset($data['id'])? $data['id'] : '';
    $result = $locationApi->updateLocation($locationparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Delete location*/
$app->post('/api/location/deleteLocation', function (Request $request, Response $response) use ($database) {
    $locationApi = new LocationApi($database);
    $data = $request->getParsedBody();
    $userid = isset($data['id'])? $data['id'] : '';
    $result = $locationApi->deleteLocation($userid);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Settings get full list*/
$app->get('/api/settings/getlist', function (Request $request, Response $response, $args) use ($database) {
    $settingsApi = new SettingsApi($database);
    $result = $settingsApi->getSettingsList();
    $response->getBody()->write(json_encode($result));
    return $response;
});


/*get setting details*/
$app->get('/api/settings/getSettingdetails/{id}', function (Request $request, Response $response, $args) use ($database) {
    $settingsApi = new SettingsApi($database);
    $id = isset($args['id'])? $args['id'] : '0';
    $result = $settingsApi->getSettingsdetails($id);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Add new settings*/
$app->post('/api/settings/addSetting', function (Request $request, Response $response) use ($database) {
    $settingsApi = new SettingsApi($database);
    $data = $request->getParsedBody();
    $settingparams = [];
    $settingparams['appname'] = isset($data['AppName'])? $data['AppName'] : '';
    $settingparams['apiurl'] = isset($data['ApiUrl'])? $data['ApiUrl'] : '';
    $settingparams['apikey'] = isset($data['ApiKey'])? $data['ApiKey'] : '';
    $settingparams['apisecret'] = isset($data['ApiSecret'])? $data['ApiSecret'] : '';
    $settingparams['additionalfields'] = isset($data['AdditionalFields'])? $data['AdditionalFields'] : '';
    $result = $settingsApi->addSetting($settingparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Update settings*/
$app->post('/api/settings/updateSetting', function (Request $request, Response $response) use ($database) {
    $settingsApi = new SettingsApi($database);
    $data = $request->getParsedBody();
    $settingparams = [];
    $settingparams['appname'] = isset($data['AppName'])? $data['AppName'] : '';
    $settingparams['apiurl'] = isset($data['ApiUrl'])? $data['ApiUrl'] : '';
    $settingparams['apikey'] = isset($data['ApiKey'])? $data['ApiKey'] : '';
    $settingparams['apisecret'] = isset($data['ApiSecret'])? $data['ApiSecret'] : '';
    $settingparams['additionalfields'] = isset($data['AdditionalFields'])? $data['AdditionalFields'] : '';
    $settingparams['id'] = isset($data['id'])? $data['id'] : '';
    $result = $settingsApi->updateSetting($settingparams);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/*Delete settings*/
$app->post('/api/settings/deleteSetting', function (Request $request, Response $response) use ($database) {
    $settingsApi = new SettingsApi($database);
    $data = $request->getParsedBody();
    $userid = isset($data['id'])? $data['id'] : '';
    $result = $settingsApi->deleteSetting($userid);
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

$app->get('/api/cart/list', function (Request $request, Response $response, $args) use ($database) {
    $CartApi = new CartApi($database);
    $userId=1;
    $result = $CartApi->getCartItems($userId);
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

$app->post('/api/cart/check', function (Request $request, Response $response, $args) use ($database) {
    $CartApi = new CartApi($database);
    $data = $request->getParsedBody();
    $result = $CartApi->checkCartItem($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/order/add', function (Request $request, Response $response, $args) use ($database) {
    $OrdersApi = new OrdersApi($database);
    $data = $request->getParsedBody();
    $result = $OrdersApi->createOrder($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/api/order/createOrderItem', function (Request $request, Response $response, $args) use ($database) {
    $OrdersApi = new OrdersApi($database);
    $data = $request->getParsedBody();
    $result = $OrdersApi->createOrderItem($data);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->run();
