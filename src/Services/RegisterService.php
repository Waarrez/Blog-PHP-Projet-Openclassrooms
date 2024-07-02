<?php

namespace Root\P5\Services;

use Exception;
use Root\P5\models\UsersRepository;

class RegisterService
{
    public function __construct(private UsersRepository $usersRepository)
    {
    }

    /**
     * Process registration data
     *
     * @throws Exception
     */
    public function register(?string $username, ?string $email, ?string $password, ?string $confirmPassword): bool
    {
        if ($this->isValidFormData($username, $email, $password, $confirmPassword)) {
            if ($password !== $confirmPassword) {
                throw new Exception('Les mots de passe ne correspondent pas !');
            }

            if ($username !== null && $email !== null && $password !== null) {
                return $this->usersRepository->createUser($username, $email, $password);
            }
        } else {
            throw new Exception('Tous les champs doivent être complétés');
        }

        return false;
    }

    /**
     * Check form data
     */
    private function isValidFormData(?string $username, ?string $email, ?string $password, ?string $confirmPassword): bool
    {
        return $username !== null && $email !== null && $password !== null && $confirmPassword !== null;
    }
}
