<?php

require __DIR__ . '/core/Database.php';
require __DIR__ . '/core/BaseModel.php';
require __DIR__ . '/core/Session.php';
require __DIR__ . '/core/Csrf.php';
require __DIR__ . '/core/Mailer.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/models/User.php';
require __DIR__ . '/models/Donation.php';
require __DIR__ . '/controllers/AuthController.php';
require __DIR__ . '/controllers/DashboardController.php';
require __DIR__ . '/controllers/DonationController.php';
require __DIR__ . '/controllers/ApiController.php';

Session::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/api/users' || $uri === '/api/donations' || $uri === '/api/orphanage/actions') {
    $api = new ApiController();
    if ($uri === '/api/users') {
        $api->users($method);
    } elseif ($uri === '/api/donations') {
        $api->donations($method);
    } else {
        $api->orphanageActions($method);
    }
    exit;
}

$routes = [
    'GET' => [
        '/' => fn() => (new DashboardController())->home(),
        '/login' => fn() => (new AuthController())->showLogin(),
        '/register' => fn() => (new AuthController())->showRegister(),
        '/logout' => fn() => (new AuthController())->logout(),
        '/dashboard' => fn() => (new DashboardController())->index(),
    ],
    'POST' => [
        '/login' => fn() => (new AuthController())->login(),
        '/register' => fn() => (new AuthController())->register(),
        '/donations/create' => fn() => (new DonationController())->create(),
        '/donations/assign' => fn() => (new DonationController())->assign(),
        '/donations/orphanage-action' => fn() => (new DonationController())->orphanageAction(),
    ],
];

if (isset($routes[$method][$uri])) {
    $routes[$method][$uri]();
    exit;
}

http_response_code(404);
echo 'Page not found';
