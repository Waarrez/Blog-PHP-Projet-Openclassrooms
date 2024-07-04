<?php

namespace Root\P5\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Random\RandomException;
use Root\P5\Manager\DatabaseConnect;
use Root\P5\Services\CSRFService;
use Root\P5\Services\MailService;
use Twig\Environment;

class HomeController extends BaseController
{
    private MailService $mailService;
    private CSRFService $CSRFService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->mailService = new MailService(new PHPMailer(true));
        $this->CSRFService = new CSRFService();
    }

    public function index(): void
    {
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        $this->render('home/index.twig', ['success' => $successMessage]);
    }

    public function pageNotFound(): void
    {
        http_response_code(404);

        $this->render("404.twig");
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


    /**
     * @throws RandomException
     */
    public function login(): void
    {
        $csrfService = $this->CSRFService;
        $csrfToken = $csrfService->generateToken();

        $this->render('login/login.twig', ['csrf_token' => $csrfToken]);
    }

    /**
     * @throws RandomException
     */
    public function register(): void
    {
        if (isset($_SESSION["user_id"])) {
            header('Location: /');
        }

        $errorMessage = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $csrfService = $this->CSRFService;
        $csrfToken = $csrfService->generateToken();

        $this->render('register/register.twig', ['error' => $errorMessage, 'csrf_token' => $csrfToken]);
    }
}
