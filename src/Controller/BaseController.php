<?php

namespace Root\P5\Controller;

use Twig\Environment;
use Root\P5\Classes\DatabaseConnect;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BaseController
{
    protected Environment $twig;
    protected DatabaseConnect $db;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        $this->twig = $twig;
        $this->db = $db;
        $this->startSession();
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function render(string $template, array $context = []): void
    {
        try {
            $context['isUserLoggedIn'] = $this->isUserLoggedIn();
            $context['loggedInUser'] = $this->getLoggedInUser();

            echo $this->twig->render($template, $context);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    protected function isUserLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    protected function getLoggedInUser(): ?array
    {
        if ($this->isUserLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'isConfirmed' => $_SESSION['isConfirmed'],
                'roles' => $_SESSION['roles'],
            ];
        }
        return null;
    }
}
