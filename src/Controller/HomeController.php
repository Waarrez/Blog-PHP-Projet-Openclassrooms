<?php

namespace Root\P5\Controller;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home/index.twig');
    }

    public function login(): void
    {
        $this->render('login/login.twig');
    }

    public function register(): void
    {
        $this->render('register/register.twig');
    }
}
