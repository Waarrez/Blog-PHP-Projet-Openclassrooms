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
                $this->setSessionUser($user, $_SESSION); // Pass $_SESSION as a parameter
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
        $requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
        $sanitizedRequestMethod = filter_var(stripslashes($requestMethod), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return $sanitizedRequestMethod !== false ? $sanitizedRequestMethod : '';
    }

    private function getPostData(string $key, int $filter)
    {
        return filter_input(INPUT_POST, $key, $filter);
    }

    private function setSessionUser($user, &$session): void
    {
        $this->startSession($session);
        $session['user_id'] = htmlspecialchars($user->id, ENT_QUOTES, 'UTF-8');
        $session['username'] = htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8');
        $session['email'] = htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8');
        $session['isConfirmed'] = htmlspecialchars($user->isConfirmed, ENT_QUOTES, 'UTF-8');
        $session['roles'] = htmlspecialchars($user->roles, ENT_QUOTES, 'UTF-8');
    }

    private function startSession(&$session): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $session = &$_SESSION;
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
