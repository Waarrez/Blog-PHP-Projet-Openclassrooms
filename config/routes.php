<?php

use FastRoute\RouteCollector;
use Root\P5\Controller\HomeController;
use Root\P5\Controller\LoginController;
use Root\P5\Controller\PostsController;
use Root\P5\Controller\RegisterController;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/posts', [PostsController::class, 'index']);
    $r->addRoute('GET', '/post/{id:\d+}', [PostsController::class, 'viewPost']);
    $r->addRoute('GET', '/login', [HomeController::class, 'login']);
    $r->addRoute('GET', '/logout', [LoginController::class, 'logout']);
    $r->addRoute('POST', '/login', [LoginController::class, 'processLoginForm']);
    $r->addRoute('GET', '/register', [HomeController::class, 'register']);
    $r->addRoute('POST', '/register', [RegisterController::class, 'processRegisterForm']);
});