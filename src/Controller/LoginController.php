<?php

namespace Root\P5\Controller;

use AllowDynamicProperties;
use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\UsersRepository;
use Twig\Environment;

#[AllowDynamicProperties]
class LoginController extends BaseController
{
    private UsersRepository $usersRepository;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->usersRepository = new UsersRepository($db);
    }

    /**
     * @throws Exception
     */
    public function processLoginForm(): void
    {
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!$email || !$password) {
                $this->render('login/login.twig', ['error' => 'Email ou mot de passe non fourni']);
                return;
            }

            $user = $this->usersRepository->loginUser($email, $password);

            if ($user !== null) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['email'] = $user->email;
                $_SESSION['isConfirmed'] = $user->isConfirmed;
                $_SESSION['roles'] = $user->roles;

                header('Location: /');
            } else {
                $this->render('login/login.twig', ['error' => 'L\'email ou le mot de passe est incorrect.']);
            }
        } else {
            $this->render('login/login.twig');
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
        header('Location: /');
    }
}
