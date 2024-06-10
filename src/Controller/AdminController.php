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

        $this->checkAdminAccess();
    }

    private function checkAdminAccess(): void
    {
        if (!$this->isAdmin()) {
            header('Location: /');
        }
    }

    private function isAdmin(): bool
    {
        return isset($_SESSION['roles']) && $_SESSION['roles'] === 'ADMIN';
    }

    public function index(): void
    {
        try {
            $users = $this->usersRepository->getUserNotApproved();
            $comments = $this->commentRepository->getUnconfirmedComments();

            $this->render('admin/index.twig', ['users' => $users, 'comments' => $comments]);
        } catch (Exception $e) {
            $this->renderError('Error: ' . $e->getMessage());
        }
    }

    public function approveComment(int $id): void
    {
        try {
            $this->commentRepository->confirmComment($id);

            $this->redirectToDashboard();
        } catch (Exception $e) {
            $this->renderError('Error: ' . $e->getMessage());
        }
    }

    public function approveUser(int $id): void
    {
        try {
            $this->usersRepository->confirmUser($id);

            $this->redirectToDashboard();
        } catch (Exception $e) {
            $this->renderError('Error: ' . $e->getMessage());
        }
    }

    private function renderError(string $message): void
    {
        $this->render('error.twig', ['message' => $message]);
    }

    private function redirectToDashboard(): void
    {
        header('Location: /dashboard_admin');
    }
}
