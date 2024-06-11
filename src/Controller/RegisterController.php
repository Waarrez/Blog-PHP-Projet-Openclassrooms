<?php

namespace Root\P5\Controller;

use AllowDynamicProperties;
use Exception;
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
     * @throws Exception
     */
    public function processRegisterForm(): void
    {
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {

            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_STRING);

            if ($username !== null && $email !== false && $password !== null && $confirmPassword !== null) {
                if ($password !== $confirmPassword) {
                    throw new Exception("Les mots de passe ne correspondent pas");
                }

                $success = $this->usersRepository->createUser($username, $email, $password);

                if ($success === true) {
                    header('Location: /');
                }

                throw new Exception("Erreur lors de la création de l'utilisateur");
            }

            throw new Exception("Tous les champs doivent être complétés");
        }
    }
}
