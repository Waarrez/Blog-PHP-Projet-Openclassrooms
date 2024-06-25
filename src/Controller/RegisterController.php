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
     * @throws Exception
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
                    $_SESSION['error'] = 'Les mots de passe ne correspondent pas !';
                    $this->redirect('/register');
                }

                if ($username !== null && $email !== null && $password !== null) {
                    $success = $this->usersRepository->createUser($username, $email, $password);

                    if ($success === true) {
                        $_SESSION['success'] = 'Votre inscription a bien été enregistrée ! Un administrateur va confirmer votre compte.';
                        $this->redirect('/');
                    } else {
                        $_SESSION['error'] = 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.';
                        $this->redirect('/register');
                    }
                }
            } else {
                $_SESSION['error'] = 'Tous les champs doivent être complétés';
                $this->redirect('/register');
            }
        } else {
            throw new InvalidArgumentException("Méthode de requête non valide");
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
