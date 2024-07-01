<?php

namespace Root\P5\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Root\P5\Manager\DatabaseConnect;
use Root\P5\Services\MailService;
use Twig\Environment;

class HomeController extends BaseController
{
    private MailService $mailService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->mailService = new MailService(new PHPMailer(true));
    }

    public function index(): void
    {
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        $this->render('home/index.twig', ['success' => $successMessage]);
    }

    public function contact(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($name && $email && $message) {
                $success = $this->mailService->sendMail($name, $email, $message);

                if ($success) {
                    $this->render('home/index.twig', ['success' => 'Votre message a bien été envoyé.']);
                } else {
                    $this->render('home/index.twig', ['error' => "Une erreur s'est produite lors de l'envoi du message."]);
                }
            } else {
                $this->render('home/index.twig', ['error' => 'Tous les champs doivent être complétés correctement.']);
            }
        } else {
            $this->render('home/index.twig');
        }
    }


    public function login(): void
    {
        $this->render('login/login.twig');
    }

    public function register(): void
    {
        $errorMessage = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->render('register/register.twig', ['error' => $errorMessage]);
    }
}
