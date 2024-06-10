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

    protected function isUserLoggedIn(array $session): bool
    {
        $session = $this->getSession($session);
        return isset($session['user_id']);
    }


    /**
     * @return array<string, mixed>|null
     */
    protected function getLoggedInUser(array $session): ?array
    {
        $session = $this->getSession($session);
        if ($this->isUserLoggedIn($session)) {
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
    private function getSession(array $session): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $session ?? [];
    }

    /**
     * @param string $template
     * @param array<string, mixed> $context
     * @param array<string, mixed> $session
     * @return string
     */
    protected function render(string $template, array $context = [], array $session = []): string
    {
        try {
            $context['isUserLoggedIn'] = $this->isUserLoggedIn($session);
            $context['loggedInUser'] = $this->getLoggedInUser($session);

            return $this->twig->render($template, $context);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    protected function isAdmin(array $session): bool
    {
        $session = $this->getSession($session);
        return isset($session['roles']) && $session['roles'] === 'ADMIN';
    }
}
