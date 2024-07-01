<?php

namespace Root\P5\Controller;

use AllowDynamicProperties;
use Exception;
use InvalidArgumentException;
use Root\P5\Manager\DatabaseConnect;
use Root\P5\models\UsersRepository;
use Root\P5\Services\RegisterService;
use Twig\Environment;

#[AllowDynamicProperties]
class RegisterController extends BaseController
{
    private RegisterService $registerService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $usersRepository = new UsersRepository($db);
        $this->registerService = new RegisterService($usersRepository);
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

            try {
                $success = $this->registerService->register($username, $email, $password, $confirmPassword);

                if ($success) {
                    $_SESSION['success'] = 'Votre inscription a bien été enregistrée ! Un administrateur va confirmer votre compte.';
                    $this->redirect('/');
                } else {
                    $_SESSION['error'] = 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.';
                    $this->redirect('/register');
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/register');
            }
        } else {
            throw new InvalidArgumentException("Méthode de requête non valide");
        }
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
