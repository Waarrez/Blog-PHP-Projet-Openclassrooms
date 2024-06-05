<?php

use FastRoute\RouteCollector;
use Root\P5\Controller\HomeController;
use Root\P5\Controller\PostsController;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/posts', [PostsController::class, 'index']);
    $r->addRoute('GET', '/post/{id:\d+}', [PostsController::class, 'viewPost']);
    $r->addRoute('GET', '/login', [HomeController::class, 'login']);
    $r->addRoute('GET', '/register', [HomeController::class, 'showFormRegister']);
    $r->addRoute('POST', '/register', [HomeController::class, 'processRegisterForm']);
});