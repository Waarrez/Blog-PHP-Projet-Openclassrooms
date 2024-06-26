<?php

namespace Root\P5\Services;

use Exception;
use Root\P5\models\User;
use Root\P5\models\UsersRepository;

class LoginService
{
    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    /**
     * Process login form data
     *
     * @param string|null $email
     * @param string|null $password
     * @throws Exception
     * @return User|null
     */
    public function processLoginForm(?string $email, ?string $password): ?User
    {
        if (!$email || !$password) {
            throw new Exception('Email ou mot de passe non fourni');
        }

        $user = $this->usersRepository->loginUser($email, $password);

        if ($user === null) {
            throw new Exception('L\'email ou le mot de passe est incorrect.');
        }

        return $user;
    }
}
