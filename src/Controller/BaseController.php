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
        $session = $this->getSession();
        return isset($session['user_id']);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getLoggedInUser(): ?array
    {
        $session = $this->getSession();
        if ($this->isUserLoggedIn()) {
            $loggedInUser = [
                'user_id' => $session['user_id'],
                'username' => $session['username'],
                'email' => $session['email'],
                'isConfirmed' => $session['isConfirmed']
            ];
            if (isset($session['roles'])) {
                $loggedInUser['roles'] = $session['roles'];
            }
            return $loggedInUser;
        }
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function getSession(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION ?? [];
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

    protected function isAdmin(): bool
    {
        $session = $this->getSession();
        return isset($session['roles']) && $session['roles'] === 'ADMIN';
    }
}
