<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\UsersRepository;
use Twig\Environment;

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
        if ($this->getRequestMethod() === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!$email || !$password) {
                $this->render('login/login.twig', ['error' => 'Email ou mot de passe non fourni']);
                return;
            }

            $user = $this->usersRepository->loginUser($email, $password);

            if ($user !== null) {
                $this->setSessionUser($user);
                $this->redirect('/');
            } else {
                $this->render('login/login.twig', ['error' => 'L\'email ou le mot de passe est incorrect.']);
            }
        } else {
            $this->render('login/login.twig');
        }
    }

    public function logout(): void
    {
        $this->startSession();
        $this->clearSession();
        $this->redirect('/');
    }

    private function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? '';
    }

    private function setSessionUser($user): void
    {
        $this->startSession();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        $_SESSION['isConfirmed'] = $user->isConfirmed;
        $_SESSION['roles'] = $user->roles;
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function clearSession(): void
    {
        session_unset();
        session_destroy();
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
