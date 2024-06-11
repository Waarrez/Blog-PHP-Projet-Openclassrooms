<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\CommentRepository;
use Root\P5\models\UsersRepository;
use Twig\Environment;

class AdminController extends BaseController
{
    private UsersRepository $usersRepository;
    private CommentRepository $commentRepository;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->usersRepository = new UsersRepository($db);
        $this->commentRepository = new CommentRepository($db);

        if (!$this->isAdmin()) {
            header('Location: /');
        }
    }

    private function isAdmin(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (isset($_SESSION['roles'])) {
                return $_SESSION['roles'] === 'ADMIN';
            }
        }
        return false;
    }

    public function index(): void
    {
        try {
            $users = $this->usersRepository->getUserNotApproved(); // Correction de la faute de frappe
            $comments = $this->commentRepository->getUnconfirmedComments();
            $this->render('admin/index.twig', ['users' => $users, 'comments' => $comments]);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    public function approveComment(int $id): void
    {
        try {
            $this->commentRepository->confirmComment($id);
            header('Location: /dashboard_admin');
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    public function approveUser(int $id): void
    {
        try {
            $this->usersRepository->confirmUser($id);
            header('Location: /dashboard_admin');
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }
}
