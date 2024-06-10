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

    private bool $sessionStarted = false;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        $this->twig = $twig;
        $this->db = $db;
        $this->startSessionIfNotStarted();
    }

    private function startSessionIfNotStarted(): void
    {
        if (!$this->sessionStarted) {
            session_start();
            $this->sessionStarted = true;
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
            $loggedInUser = [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'isConfirmed' => $_SESSION['isConfirmed']
            ];
            if (isset($_SESSION['roles'])) {
                $loggedInUser['roles'] = $_SESSION['roles'];
            }
            return $loggedInUser;
        }
        return null;
    }

    /**
     * @param string $template
     * @param array<string, mixed> $context
     * @return string
     */
    protected function render(string $template, array $context = []): string
    {
        try {
            $context['isUserLoggedIn'] = $this->isUserLoggedIn();
            $context['loggedInUser'] = $this->getLoggedInUser();

            return $this->twig->render($template, $context);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
