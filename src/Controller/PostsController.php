<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Manager\DatabaseConnect;
use Root\P5\models\CommentRepository;
use Root\P5\models\PostsRepository;
use Root\P5\Services\PostService;
use Twig\Environment;

class PostsController extends BaseController
{
    private PostService $postService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $postsRepository = new PostsRepository($db);
        $commentsRepository = new CommentRepository($db);
        $this->postService = new PostService($postsRepository, $commentsRepository);
    }

    /**
     * @throws Exception
     */
    public function index(): void
    {
        try {
            $posts = $this->postService->getPosts();
            $this->render('posts/index.twig', ['posts' => $posts]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la récupération des articles.']);
        }
    }

    public function dashboardPosts(): void
    {
        if (!isset($_SESSION["user_id"])) {
            $this->render('/login');
        }

        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        $userConfirmed = $_SESSION["isConfirmed"] ?? false;
        if (!$userConfirmed) {
            $this->redirect('/');
        }

        try {
            $userId = $_SESSION["user_id"];
            $posts = $this->postService->getPostsByUser($userId);
            $this->render('posts/dashboard_post.twig', ['posts' => $posts, 'success' => $successMessage]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la récupération des articles du tableau de bord.']);
        }
    }

    public function addPost(): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        $this->render('posts/add_post.twig');
    }

    /**
     * @throws Exception
     */
    public function addPostForm(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION["user_id"])) {
                $this->redirect('/login');
            }

            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $author = $_SESSION['username'] ?? '';
            $userId = $_SESSION['user_id'] ?? null;

            if (!empty($title) && !empty($chapo) && !empty($content) && $userId !== null) {
                $success = $this->postService->addPost($title, $chapo, $content, $userId, $author);

                if ($success) {
                    $_SESSION['success'] = 'Votre article à bien été ajouté ';
                    $this->redirect('/dashboard_posts');
                } else {
                    $this->render('error.twig', ['message' => 'Erreur lors de la création du post']);
                }
            } else {
                $this->render('error.twig', ['message' => 'Tous les champs doivent être complétés']);
            }
        }
    }

    public function viewPost(string $slug): void
    {
        $successMessage = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        try {
            $post = $this->postService->getPostBySlug($slug);
            $comments = $this->postService->getCommentsByPost($slug);

            $userIsAuthenticated = isset($_SESSION['user_id']);

            $this->render('posts/view_post.twig', [
                'post' => $post,
                'comments' => $comments,
                'userIsAuthenticated' => $userIsAuthenticated,
                'success' => $successMessage
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => $e->getMessage()]);
        }
    }

    public function addComment(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION["user_id"])) {
                $this->redirect('/login');
            }

            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $userId = $_SESSION['user_id'] ?? null;
            $slug = filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!empty($content) && $userId !== null && !empty($slug)) {
                try {
                    $success = $this->postService->addComment($slug, $content, $userId);

                    if ($success) {
                        $_SESSION['success'] = 'Votre commentaire a bien été envoyé ! Un administrateur doit le confirmer.';
                        header("Location: /post/{$slug}");
                    } else {
                        $this->render('error.twig', ['message' => 'Erreur lors de l\'ajout du commentaire']);
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de l\'ajout du commentaire.']);
                }
            } else {
                $this->render('error.twig', ['message' => 'Le champ de commentaire ne peut pas être vide']);
            }
        }
    }

    public function editPost(string $slug): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        if (empty($slug)) {
            $this->render('error.twig', ['message' => 'Slug de publication invalide']);
            return;
        }

        try {
            $post = $this->postService->getPostBySlug($slug);

            if ($post === null) {
                $this->render('error.twig', ['message' => 'Article non trouvé']);
                return;
            }

            if (trim(strval($post->getUserId())) !== trim(strval($_SESSION['user_id']))) {
                $this->render('error.twig', ['message' => 'Vous n\'avez pas l\'autorisation de modifier cet article.']);
                return;
            }

            $this->render('posts/edit_post.twig', ['post' => $post]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la récupération de l\'article pour l\'édition.']);
        }
    }

    /**
     * @throws Exception
     */
    public function editPostForm(): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $author = $_SESSION['username'] ?? '';
            $userId = $_SESSION['user_id'] ?? null;
            $postId = filter_input(INPUT_POST, 'postId', FILTER_VALIDATE_INT);

            if (!empty($title) && !empty($chapo) && !empty($content) && $userId !== null && $postId !== false) {
                $success = $this->postService->editPost((int)$postId, $title, $chapo, $content, $author, $userId);

                if ($success) {
                    $_SESSION['success'] = 'Votre article à bien été modifié ';
                    header('Location: /dashboard_posts');
                } else {
                    $this->render('error.twig', ['message' => 'Erreur lors de la modification de l\'article']);
                }
            } else {
                $this->render('error.twig', ['message' => 'Tous les champs doivent être complétés']);
            }
        }
    }

    public function deletePost(string $slug): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        try {
            $success = $this->postService->deletePost($slug);
            if ($success) {
                $_SESSION['success'] = 'Votre article à bien été supprimé ';
                header('Location: /dashboard_posts');
            } else {
                $this->render('error.twig', ['message' => 'Erreur lors de la suppression de l\'article']);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la suppression de l\'article.']);
        }
    }
}
