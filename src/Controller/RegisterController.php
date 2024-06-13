<?php

namespace Root\P5\Controller;

use AllowDynamicProperties;
use Exception;
use InvalidArgumentException;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\UsersRepository;
use Twig\Environment;

#[AllowDynamicProperties]
class RegisterController extends BaseController
{
    private UsersRepository $usersRepository;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->usersRepository = new UsersRepository($db);
    }

    /**
     * Process form data
     *
     * @throws Exception En cas d'erreur pendant le traitement du formulaire
     */
    public function processRegisterForm(): void
    {
        if ($this->getRequestMethod() === "POST") {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: null;
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
            $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;

            if ($this->isValidFormData($username, $email, $password, $confirmPassword)) {
                if ($password !== $confirmPassword) {
                    throw new InvalidArgumentException("Les mots de passe ne correspondent pas");
                }

                // Ensure $username, $email, and $password are not null
                if ($username !== null && $email !== null && $password !== null) {
                    $success = $this->usersRepository->createUser($username, $email, $password);

                    if ($success === true) {
                        $this->redirect('/');
                    }

                    throw new Exception("Erreur lors de la création de l'utilisateur");
                }
            }

            throw new InvalidArgumentException("Tous les champs doivent être complétés");
        }
    }

    /**
     * Check form data
     *
     * @param string|null $username Username
     * @param string|null $email Email
     * @param string|null $password Password
     * @param string|null $confirmPassword Confirm Password
     * @return bool True if valid data
     */
    private function isValidFormData(?string $username, ?string $email, ?string $password, ?string $confirmPassword): bool
    {
        return $username !== null && $email !== null && $password !== null && $confirmPassword !== null;
    }

    /**
     * Redirect to route
     *
     * @param string $url
     */
    private function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }

    /**
     * Retrieve method HTTP
     *
     * @return string
     */
    private function getRequestMethod(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        return is_string($method) ? $method : 'GET';
    }
}
