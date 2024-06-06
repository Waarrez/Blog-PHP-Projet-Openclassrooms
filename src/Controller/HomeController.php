<?php

namespace Root\P5\Controller;

use AllowDynamicProperties;
use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\UsersRepository;
use Twig\Environment;

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
