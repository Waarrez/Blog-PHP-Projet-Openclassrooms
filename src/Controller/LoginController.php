<?php

namespace Root\P5\Controller;

use AllowDynamicProperties;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\UsersRepository;
use Twig\Environment;

#[AllowDynamicProperties] class LoginController extends BaseController
{
    private UsersRepository $usersRepository;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->userRepository = new UsersRepository($db);
    }

    /**
     * @throws Exception
     */
    public function processLoginForm(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userRepository->loginUser($email, $password);

            if ($user !== null) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['email'] = $user->email;
                $_SESSION['isConfirmed'] = $user->isConfirmed;
                $_SESSION['roles'] = $user->roles;

                header('Location: /');
                exit();
            } else {
                echo "L'email ou le mot de passe est incorrect.";
            }
        } else {
            $this->render('login/login.twig');
        }
    }

    public function login(): void
    {
        $this->render('login/login.twig');
    }

    #[NoReturn] public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /');
        exit();
    }
}
