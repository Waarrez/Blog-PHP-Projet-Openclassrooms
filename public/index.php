<?php

use Root\P5\Classes\DatabaseConnect;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/errors.php';

$twig = require __DIR__ . '/../config/twig.php';
$dispatcher = require __DIR__ . '/../config/routes.php';

$dbConnect = new DatabaseConnect();

$httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
$uri = $_SERVER['REQUEST_URI'] ?? '';
$pos = strpos($uri, '?');


if ($pos !== false) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        handleNotFound();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        handleMethodNotAllowed();
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        try {
            handleRouteFound($handler, $twig, $dbConnect, $vars);
        } catch (Exception $e) {
            handleError($e);
        }
        break;
}

function handleNotFound(): void
{
    http_response_code(404);
    header('HTTP/1.0 404 Not Found');
}

function handleMethodNotAllowed(): void
{
    http_response_code(405);
    header("HTTP/1.1 405 Method Not Allowed");
}

/**
 * @param array{string, string} $handler
 * @param array<string, mixed> $vars
 * @throws Exception
 */
function handleRouteFound(array $handler, Environment $twig, DatabaseConnect $dbConnect, array $vars): void
{
    if (count($handler) != 2) {
        throw new Exception("Le handler n'est pas un tableau valide.");
    }

    $controllerName = $handler[0];
    $methodName = $handler[1];

    if (!class_exists($controllerName)) {
        throw new Exception("Le contrôleur $controllerName n'existe pas.");
    }

    $controller = new $controllerName($twig, $dbConnect);

    if (!method_exists($controller, $methodName)) {
        throw new Exception("La méthode " . htmlspecialchars($methodName, ENT_QUOTES, 'UTF-8') . " n'existe pas sur le contrôleur " . htmlspecialchars($controllerName, ENT_QUOTES, 'UTF-8') . ".");
    }

    $controller->{$methodName}($vars);
}

/**
 * @throws RuntimeError
 * @throws SyntaxError
 * @throws LoaderError
 */
function handleError(Exception $e, Environment $twig): void
{
    renderError($e->getMessage(), $twig);
}

/**
 * @throws RuntimeError
 * @throws SyntaxError
 * @throws LoaderError
 */
function renderError(string $errorMessage, Environment $twig): void
{
    echo $twig->render('error.twig', ['errorMessage' => $errorMessage]);
}