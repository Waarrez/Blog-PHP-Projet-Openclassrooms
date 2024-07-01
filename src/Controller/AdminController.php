<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Manager\DatabaseConnect;
use Root\P5\models\CommentRepository;
use Root\P5\models\UsersRepository;
use Root\P5\Services\AdminService;
use Twig\Environment;

class AdminController extends BaseController
{
    private AdminService $adminService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $usersRepository = new UsersRepository($db);
        $commentRepository = new CommentRepository($db);
        $this->adminService = new AdminService($usersRepository, $commentRepository);

        if (!$this->isAdmin()) {
            $this->redirect('/');
        }
    }

    private function getUserRole(): string
    {
        $roles = isset($_SESSION['roles']) ? filter_var($_SESSION['roles'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
        return !empty($roles) ? $roles : 'USER';
    }

    private function isAdmin(): bool
    {
        return $this->getUserRole() === 'ADMIN';
    }

    public function index(): void
    {
        try {
            $users = $this->adminService->getUnapprovedUsers();
            $comments = $this->adminService->getUnconfirmedComments();
            $this->render('admin/index.twig', ['users' => $users, 'comments' => $comments]);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    public function approveComment(int $id): void
    {
        try {
            $this->adminService->approveComment($id);
            $this->redirect('/dashboard_admin');
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    public function approveUser(int $id): void
    {
        try {
            $this->adminService->approveUser($id);
            $this->redirect('/dashboard_admin');
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }
}
