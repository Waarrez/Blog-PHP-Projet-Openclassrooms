<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\User;
use Root\P5\models\UsersRepository;
use Root\P5\Services\LoginService;
use Twig\Environment;

class LoginController extends BaseController
{
    private LoginService $loginService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $usersRepository = new UsersRepository($db);
        $this->loginService = new LoginService($usersRepository);
    }

    /**
     * @throws Exception
     */
    public function processLoginForm(): void
    {
        if ($this->getRequestMethod() === 'POST') {
            $email = $this->getPostData('email', FILTER_SANITIZE_EMAIL);
            $password = $this->getPostData('password', FILTER_SANITIZE_SPECIAL_CHARS);

            try {
                $user = $this->loginService->processLoginForm($email, $password);

                if ($user !== null) {
                    $this->setSessionUser($user);
                    $this->redirect('/');
                    return;
                }
            } catch (Exception $e) {
                $this->render('login/login.twig', ['error' => $e->getMessage()]);
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
        $sanitizedRequestMethod = filter_var(stripslashes($requestMethod), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return $sanitizedRequestMethod !== false ? $sanitizedRequestMethod : '';
    }

    private function getPostData(string $key, int $filter): ?string
    {
        return filter_input(INPUT_POST, $key, $filter);
    }

    private function setSessionUser(User $user): void
    {
        $this->startSession();
        $_SESSION['user_id'] = htmlspecialchars(strval($user->getId()), ENT_QUOTES, 'UTF-8');
        $_SESSION['username'] = htmlspecialchars(strval($user->getUsername()), ENT_QUOTES, 'UTF-8');
        $_SESSION['email'] = htmlspecialchars(strval($user->getEmail()), ENT_QUOTES, 'UTF-8');
        $_SESSION['isConfirmed'] = htmlspecialchars(strval($user->isConfirmed()), ENT_QUOTES, 'UTF-8');
        $_SESSION['roles'] = htmlspecialchars(strval($user->getRoles()), ENT_QUOTES, 'UTF-8');
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
}

