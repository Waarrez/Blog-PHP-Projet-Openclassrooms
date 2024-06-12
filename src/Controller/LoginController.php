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
            $email = $this->getPostData('email', FILTER_SANITIZE_EMAIL);
            $password = $this->getPostData('password', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!$email || !$password) {
                $this->render('login/login.twig', ['error' => 'Email ou mot de passe non fourni']);
                return;
            }

            $user = $this->usersRepository->loginUser($email, $password);

            if ($user !== null) {
                $this->setSessionUser($user);
                $this->redirect('/');
                return;
            } else {
                $this->render('login/login.twig', ['error' => 'L\'email ou le mot de passe est incorrect.']);
                return;
            }
        }

        $this->render('login/login.twig');
    }

    public function logout(): void
    {
        $this->startSession();
        $this->clearSession();
        $this->redirect('/');
    }

    private function getRequestMethod(): string
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $sanitizedRequestMethod = filter_var($requestMethod, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        return $sanitizedRequestMethod !== false ? $sanitizedRequestMethod : '';
    }

    private function getPostData(string $key, int $filter)
    {
        return filter_input(INPUT_POST, $key, $filter);
    }

    private function setSessionUser($user): void
    {
        $this->startSession();
        $_SESSION['user_id'] = htmlspecialchars($user->id, ENT_QUOTES, 'UTF-8');
        $_SESSION['username'] = htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8');
        $_SESSION['email'] = htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8');
        $_SESSION['isConfirmed'] = htmlspecialchars($user->isConfirmed, ENT_QUOTES, 'UTF-8');
        $_SESSION['roles'] = htmlspecialchars($user->roles, ENT_QUOTES, 'UTF-8');
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
    }
}
