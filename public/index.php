<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/errors.php';

$twig = require __DIR__ . '/../config/twig.php';
$dispatcher = require __DIR__ . '/../config/routes.php';

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