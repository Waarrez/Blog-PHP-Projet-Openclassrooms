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
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            if (!empty($username) && !empty($email) && !empty($password) && !empty($confirmPassword)) {
                if ($password !== $confirmPassword) {
                    echo "Les mots de passe ne correspondent pas";
                    exit();
                }

                $success = $this->usersRepository->createUser($username, $email, $password); // Correction de la faute de frappe

                if ($success) {
                    header('Location: /');
                } else {
                    echo "Erreur lors de la création de l'utilisateur";
                    exit();
                }
            } else {
                echo "Tous les champs doivent être complétés";
            }
        }
    }
}
