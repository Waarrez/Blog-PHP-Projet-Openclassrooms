<?php

use FastRoute\RouteCollector;
use Root\P5\Controller\AdminController;
use Root\P5\Controller\HomeController;
use Root\P5\Controller\LoginController;
use Root\P5\Controller\PostsController;
use Root\P5\Controller\RegisterController;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('POST', '/', [HomeController::class, 'contact']);
    $r->addRoute('GET', '/posts', [PostsController::class, 'index']);
    $r->addRoute('GET', '/dashboard_posts', [PostsController::class, 'dashboardPosts']);
    $r->addRoute('GET', '/add_post', [PostsController::class, 'addPost']);
    $r->addRoute('POST', '/add_post', [PostsController::class, 'addPostForm']);
    $r->addRoute('GET', '/edit_post/{slug:[\w-]+}', [PostsController::class, 'editPost']);
    $r->addRoute('POST', '/edit_post/{slug:[\w-]+}', [PostsController::class, 'editPostForm']);
    $r->addRoute('GET', '/delete_post/{slug:[\w-]+}', [PostsController::class, 'deletePost']);
    $r->addRoute('GET', '/post/{slug:[\w-]+}', [PostsController::class, 'viewPost']);
    $r->addRoute('GET', '/login', [HomeController::class, 'login']);
    $r->addRoute('GET', '/logout', [LoginController::class, 'logout']);
    $r->addRoute('POST', '/login', [LoginController::class, 'processLoginForm']);
    $r->addRoute('GET', '/register', [HomeController::class, 'register']);
    $r->addRoute('POST', '/register', [RegisterController::class, 'processRegisterForm']);
    $r->addRoute('POST', '/add_comment', [PostsController::class, 'addComment']);
    $r->addRoute('GET', '/dashboard_admin', [AdminController::class, 'index']);
    $r->addRoute('POST', '/approveC/{id:\d+}', [AdminController::class, 'approveComment']);
    $r->addRoute('POST', '/approveU/{id:\d+}', [AdminController::class, 'approveUser']);
});
