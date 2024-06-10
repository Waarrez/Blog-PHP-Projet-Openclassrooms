<?php

use Root\P5\Classes\DatabaseConnect;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/errors.php';

$twig = require __DIR__ . '/../config/twig.php';
$dispatcher = require __DIR__ . '/../config/routes.php';

$dbConnect = new DatabaseConnect();

$httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
$uri = $_SERVER['REQUEST_URI'] ?? '';

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        header('HTTP/1.0 404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        header("HTTP/1.1 405 Method Not Allowed");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        try {
            if (!is_array($handler) || count($handler) !== 2) {
                throw new Exception("Le handler n'est pas un tableau valide.");
            }

            $controllerName = $handler[0];
            $methodName = $handler[1];

            if (!class_exists($controllerName)) {
                throw new Exception("Le contrôleur $controllerName n'existe pas.");
            }

            $controller = new $controllerName($twig, $dbConnect);

            if (!method_exists($controller, $methodName)) {
                throw new Exception("La méthode $methodName n'existe pas sur le contrôleur $controllerName.");
            }

            if (method_exists($controller, $methodName)) {
                $controller->{$methodName}($vars);
            } else {
                throw new Exception("La méthode $methodName n'existe pas sur le contrôleur $controllerName.");
            }
        } catch (Exception $e) {
            echo 'Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        break;
}
