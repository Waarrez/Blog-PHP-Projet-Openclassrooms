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

    /**
     * @param array<string, mixed> $context
     */
    protected function render(string $template, array $context = []): void
    {
        try {
            $context['isUserLoggedIn'] = $this->isUserLoggedIn();
            $context['loggedInUser'] = $this->getLoggedInUser();

            echo $this->twig->render($template, $context);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            error_log($e->getMessage());
            $this->renderErrorPage('Une erreur est survenue lors du rendu de la page.');
        }
    }

    private function renderErrorPage(string $message): void
    {
        try {
            echo $this->twig->render('error.twig', ['message' => $message]);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            error_log($e->getMessage());
        }
    }

    protected function isUserLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getLoggedInUser(): ?array
    {
        if ($this->isUserLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null,
                'email' => $_SESSION['email'] ?? null,
                'isConfirmed' => $_SESSION['isConfirmed'] ?? null,
                'roles' => $_SESSION['roles'] ?? null,
            ];
        }
        return null;
    }
}
