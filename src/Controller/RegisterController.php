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
        // Vérifie si la méthode de requête est POST
        if ($this->getRequestMethod() === "POST") {

            // Récupère et filtre les données du formulaire
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
            $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_SPECIAL_CHARS);

            // Vérifie que toutes les variables sont présentes et non vides
            if ($this->isValidFormData($username, $email, $password, $confirmPassword)) {
                if ($password !== $confirmPassword) {
                    throw new InvalidArgumentException("Les mots de passe ne correspondent pas");
                }

                // Création de l'utilisateur
                $success = $this->usersRepository->createUser($username, $email, $password);

                // Redirige en cas de succès, sinon lance une exception
                if ($success === true) {
                    $this->redirect('/');
                }

                throw new Exception("Erreur lors de la création de l'utilisateur");
            }

            throw new InvalidArgumentException("Tous les champs doivent être complétés");
        }
    }

    /**
     * Vérifie si les données du formulaire sont valides.
     *
     * @param string|null $username Le nom d'utilisateur
     * @param string|false $email L'email
     * @param string|null $password Le mot de passe
     * @param string|null $confirmPassword La confirmation du mot de passe
     * @return bool True si les données sont valides, sinon False
     */
    private function isValidFormData($username, $email, $password, $confirmPassword): bool
    {
        return $username !== null && $email !== false && $password !== null && $confirmPassword !== null;
    }

    /**
     * Redirige vers une URL donnée.
     *
     * @param string $url L'URL vers laquelle rediriger
     */
    private function redirect(string $url): void
    {
        header("Location: $url");
        return;
    }

    /**
     * Récupère la méthode de la requête HTTP.
     *
     * @return string La méthode de la requête HTTP
     */
    private function getRequestMethod(): string
    {
        return filter_var($_SERVER['REQUEST_METHOD'] ?? 'GET', FILTER_SANITIZE_SPECIAL_CHARS);
    }
}
