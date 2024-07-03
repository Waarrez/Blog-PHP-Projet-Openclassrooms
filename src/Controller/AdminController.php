<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Manager\DatabaseConnect;
use Root\P5\models\CommentRepository;
use Root\P5\models\PostsRepository;
use Root\P5\models\UsersRepository;
use Root\P5\Services\AdminService;
use Root\P5\Services\PostService;
use Twig\Environment;

class AdminController extends BaseController
{
    private AdminService $adminService;
    private PostService $postService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $usersRepository = new UsersRepository($db);
        $commentRepository = new CommentRepository($db);
        $postsRepository = new PostsRepository($db);
        $this->adminService = new AdminService($usersRepository, $commentRepository);
        $this->postService = new PostService($postsRepository, $commentRepository);

        if (!$this->isAdmin()) {
            $this->redirect('/');
        }
    }

    private function getUserRole(): string
    {
        $roles = isset($_SESSION['roles']) ? filter_var($_SESSION['roles'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
        return empty($roles) ? 'USER' : $roles;
    }

    private function isAdmin(): bool
    {
        return $this->getUserRole() === 'ADMIN';
    }

    public function index(): void
    {
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        try {
            $users = $this->adminService->getUnapprovedUsers();
            $comments = $this->adminService->getUnconfirmedComments();
            $posts = $this->postService->getPosts();
            $this->render('admin/index.twig', ['users' => $users, 'comments' => $comments, 'posts' => $posts ,'success' => $successMessage]);
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    public function approveComment(int $id): void
    {
        try {
            $success = $this->adminService->approveComment($id);

            if ($success) {
                $_SESSION['success'] = 'Le commentaire a été approuvé avec succès.';
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'approbation du commentaire. Veuillez réessayer.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/dashboard_admin');
    }

    public function deleteComment(int $id): void
    {
        try {
            $success = $this->adminService->deleteComment($id);

            if ($success) {
                $_SESSION['success'] = 'Le commentaire a été supprimé avec succès.';
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de la suppression du commentaire. Veuillez réessayer.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/dashboard_admin');
    }

    public function approveUser(int $id): void
    {
        try {
            $success = $this->adminService->approveUser($id);

            if ($success) {
                $_SESSION['success'] = 'L\'utilisateur a été approuvé avec succès.';
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'approbation de l\'utilisateur. Veuillez réessayer.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/dashboard_admin');
    }


    public function deleteUser(int $id): void
    {
        try {
            $success = $this->adminService->deleteUser($id);

            if ($success) {
                $_SESSION['success'] = 'L\'utilisateur a été supprimé avec succès.';
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'approbation de l\'utilisateur. Veuillez réessayer.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/dashboard_admin');
    }

    public function deletePost(string $slug): void
    {
        try {
            $success = $this->postService->deletePost($slug);
            if ($success) {
                $_SESSION['success'] = 'Votre article à bien été supprimé ';
                header('Location: /dashboard_admin');
            } else {
                $this->render('error.twig', ['message' => 'Erreur lors de la suppression de l\'article']);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la suppression de l\'article.']);
        }
    }
}
