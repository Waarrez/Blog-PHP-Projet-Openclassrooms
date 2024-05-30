<?php

require __DIR__ . '/../vendor/autoload.php';
require_once "../includes/connection.php";

use FastRoute\RouteCollector;
use Root\P5\Controller\HomeController;
use function FastRoute\simpleDispatcher;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$loader = new FilesystemLoader(__DIR__ . '/../src/templates');
$twig = new Environment($loader);

// Configuration des routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        try {
            $controller = new $handler[0]($twig);
            call_user_func_array([$controller, $handler[1]], $vars);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        break;
}
