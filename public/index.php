<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\MailController;
use Illuminate\Http\Request;
use App\Router;
use Illuminate\Http\JsonResponse;

$request = Request::capture();
$router = new Router();

$router->post('/api/login', function ($request) {
    $controller = new AuthController();
    return $controller->login($request);
});

$router->post('/api/register', function ($request) {
    $controller = new AuthController();
    return $controller->register($request);
});

$router->post('/api/send-email', function ($request) {
    // authenticate
    $auth = authenticate();
    if (isset($auth['status']) && !$auth['status']) {
        return new JsonResponse($auth, JsonResponse::HTTP_FORBIDDEN);
    }

    $controller = new MailController();
    return $controller->create($request);
});

$respond = $router->run($request);
$respond->send();
