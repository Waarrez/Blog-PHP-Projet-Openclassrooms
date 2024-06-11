<?php

use Root\P5\Classes\DatabaseConnect;
use Twig\Environment;
use FastRoute\Dispatcher;

$autoloadPath = realpath(__DIR__ . '/../vendor/autoload.php');
$twigConfigPath = realpath(__DIR__ . '/../config/twig.php');
$routesConfigPath = realpath(__DIR__ . '/../config/routes.php');

if ($autoloadPath === FALSE || $twigConfigPath === FALSE || $routesConfigPath === FALSE) {
    throw new Exception('Un chemin de fichier nécessaire est invalide.');
}

require_once $autoloadPath;

$twig = require $twigConfigPath;
$dispatcher = require $routesConfigPath;

$dbConnect = new DatabaseConnect();

$httpMethod = isset($_SERVER['REQUEST_METHOD']) ? stripslashes($_SERVER['REQUEST_METHOD']) : '';
$uri = isset($_SERVER['REQUEST_URI']) ? stripslashes($_SERVER['REQUEST_URI']) : '';

if (!empty($uri) && false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        http_response_code(404);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        break;
    case Dispatcher::FOUND:
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

            foreach ($vars as $key => $value) {
                if (is_numeric($value)) {
                    $vars[$key] = (int)$value;
                }
            }

            $controller->{$methodName}(...array_values($vars));
        } catch (Exception $e) {
            http_response_code(500);
            error_log('Error: ' . $e->getMessage());
        }
        break;
}
