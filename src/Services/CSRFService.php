<?php

namespace Root\P5\Services;

use Random\RandomException;

class CSRFService
{
    /**
     * @return string
     * @throws RandomException
     */
    public function generateToken(): string
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;

        return $token;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function validateToken(string $token): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            unset($_SESSION['csrf_token']);
            return true;
        }

        return false;
    }
}
