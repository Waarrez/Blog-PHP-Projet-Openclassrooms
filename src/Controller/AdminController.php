<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\CommentRepository;
use Root\P5\models\UsersRepository;
use Twig\Environment;

class AdminController extends BaseController
{
    /**
     * @var UsersRepository Instance of UsersRepository.
     */
    private UsersRepository $usersRepository;

    /**
     * @var CommentRepository Instance of CommentRepository.
     */
    private CommentRepository $commentRepository;

    /**
     * AdminController constructor.
     *
     * @param Environment     $twig Twig environment.
     * @param DatabaseConnect $db   Database connection.
     */
    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->usersRepository = new UsersRepository($db);
        $this->commentRepository = new CommentRepository($db);

        if (!$this->isAdmin()) {
            header('Location: /');
            exit(); // Stop further execution to prevent unauthorized access.
        }
    }

    /**
     * Checks if the current user is an admin.
     *
     * @return bool True if the user is an admin, false otherwise.
     */
    private function isAdmin(): bool
    {
        return (isset($_SESSION['roles']) && $_SESSION['roles'] === 'ADMIN');
    }

    /**
     * Displays the admin page with unapproved users and unconfirmed comments.
     */
    public function index(): void
    {
        try {
            $users = $this->usersRepository->getUserNotApproved(); // Fixing typo
            $comments = $this->commentRepository->getUnconfirmedComments();
            $this->render('admin/index.twig', ['users' => $users, 'comments' => $comments]);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    /**
     * Approves a specific comment based on its ID.
     *
     * @param int $id The ID of the comment to approve.
     */
    public function approveComment(int $id): void
    {
        try {
            $this->commentRepository->confirmComment($id);
            header('Location: /dashboard_admin');
            exit(); // Stop further execution after redirect.
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    /**
     * Approves a specific user based on their ID.
     *
     * @param int $id The ID of the user to approve.
     */
    public function approveUser(int $id): void
    {
        try {
            $this->usersRepository->confirmUser($id);
            header('Location: /dashboard_admin');
            exit(); // Stop further execution after redirect.
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }
}
