<?php

namespace Root\P5\Controller;

use AllowDynamicProperties;
use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\UsersRepository;
use Twig\Environment;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home/index.twig');
    }

    public function contact(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';

            $to = "thimote.cabotte6259@gmail.com";
            $subject = "Nouveau message de contact";
            $body = "Nom/prénom: $nom\n";
            $body .= "Email de contact: $email\n";
            $body .= "Message:\n$message";

            if (mail($to, $subject, $body)) {
                echo "Votre message a bien été envoyé.";
            } else {
                echo "Une erreur s'est produite lors de l'envoi du message.";
            }
        } else {
            echo "La requête n'est pas de type POST.";
        }
    }

    public function login(): void
    {
        $this->render('login/login.twig');
    }

    public function register(): void
    {
        $this->render('register/register.twig');
    }
}
