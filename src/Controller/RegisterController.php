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
     * Traite le formulaire d'inscription.
     *
     * @throws Exception En cas d'erreur pendant le traitement du formulaire
     */
    public function processRegisterForm(): void
    {
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {

            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);

            if ($username && $email && $password && $confirmPassword) {
                if ($password !== $confirmPassword) {
                    throw new InvalidArgumentException("Les mots de passe ne correspondent pas");
                }

                // Création de l'utilisateur
                $success = $this->usersRepository->createUser($username, $email, $password);

                // Redirige en cas de succès, sinon lance une exception
                if ($success) {
                    $this->redirect('/');
                } else {
                    throw new Exception("Erreur lors de la création de l'utilisateur");
                }
            } else {
                throw new InvalidArgumentException("Tous les champs doivent être complétés");
            }
        }
    }

    /**
     * Redirige vers une URL donnée.
     *
     * @param string $url L'URL vers laquelle rediriger
     */
    private function redirect(string $url): void
    {
        header("Location: $url");
    }
}
