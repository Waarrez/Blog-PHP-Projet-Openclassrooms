<?php

namespace Root\P5\Controller;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home/index.twig');
    }

    public function contact(): void
    {
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($nom && $email && $message) {
                $to = "thimote.cabotte6259@gmail.com";
                $subject = "Nouveau message de contact";
                $body = "Nom/prénom: $nom\n";
                $body .= "Email de contact: $email\n";
                $body .= "Message:\n$message";

                if (mail($to, $subject, $body)) {
                    $this->render('contact/contact.twig', ['success' => 'Votre message a bien été envoyé.']);
                } else {
                    $this->render('contact/contact.twig', ['error' => 'Une erreur s\'est produite lors de l\'envoi du message.']);
                }
            } else {
                $this->render('contact/contact.twig', ['error' => 'Tous les champs doivent être complétés correctement.']);
            }
        } else {
            $this->render('contact/contact.twig');
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
