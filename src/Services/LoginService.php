<?php

namespace Root\P5\Services;

use Exception;
use Root\P5\models\User;
use Root\P5\models\UsersRepository;

class LoginService
{
    public function __construct(private readonly UsersRepository $usersRepository)
    {
    }

    /**
     * Process login form data
     *
     * @throws Exception
     */
    public function processLoginForm(?string $email, ?string $password): ?User
    {
        if (!$email || !$password) {
            throw new Exception('Email ou mot de passe non fourni');
        }

        $user = $this->usersRepository->loginUser($email, $password);

        if (!$user instanceof \Root\P5\models\User) {
            throw new Exception('L\'email ou le mot de passe est incorrect.');
        }

        return $user;
    }
}
