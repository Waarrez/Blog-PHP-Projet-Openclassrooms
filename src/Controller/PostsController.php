<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\CommentRepository;
use Root\P5\models\PostsRepository;
use Twig\Environment;

class PostsController extends BaseController
{
    private PostsRepository $postsRepository;
    private CommentRepository $commentsRepository;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->postsRepository = new PostsRepository($db);
        $this->commentsRepository = new CommentRepository($db);
    }

    /**
     * @throws Exception
     */
    public function index(): void
    {
        try {
            $posts = $this->postsRepository->getPosts();
            $this->render('posts/index.twig', ['posts' => $posts]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la récupération des articles.']);
        }
    }

    public function dashboardPosts(): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        $successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : null;
        unset($_SESSION['success']);

        $userConfirmed = $_SESSION["isConfirmed"] ?? false;
        if (!$userConfirmed) {
            header('Location: /');
        }

        try {
            $userId = $_SESSION["user_id"];
            $posts = $this->postsRepository->getPostsByUser($userId);
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
                header('Location: /login');
            }

            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $author = $_SESSION['username'] ?? '';
            $userId = $_SESSION['user_id'] ?? null;

            if (!empty($title) && !empty($chapo) && !empty($content) && $userId !== null) {
                $success = $this->postsRepository->addPost($title, $chapo, $content, $userId, $author);

                if ($success) {
                    $_SESSION['success'] = 'Votre article à bien été ajouté ';
                    header('Location: /dashboard_posts');
                } else {
                    $this->render('error.twig', ['message' => 'Erreur lors de la création du post']);
                }
            } else {
                $this->render('error.twig', ['message' => 'Tous les champs doivent être complétés']);
            }
        }
    }

    public function viewPost(int $postId): void
    {
        try {
            $post = $this->postsRepository->getPostById($postId);
            $comments = $this->commentsRepository->getCommentsByPost($postId);

            $userIsAuthenticated = isset($_SESSION['user_id']);

            $this->render('posts/view_post.twig', [
                'post' => $post,
                'comments' => $comments,
                'userIsAuthenticated' => $userIsAuthenticated
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la récupération de l\'article.']);
        }
    }

    public function addComment(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION["user_id"])) {
                header('Location: /login');
            }

            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $userId = $_SESSION['user_id'] ?? null;
            $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

            if (!empty($content) && $userId !== null && $postId !== false) {
                try {
                    $success = $this->commentsRepository->addComment((int)$postId, $content, $userId);

                    if ($success) {
                        $_SESSION['success'] = 'Votre commentaire à bien été envoyé ! Un administrateur doit le confirmer.';
                        header("Location: /post/{$postId}");
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

    public function editPost(int $postId): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        if (!filter_var($postId, FILTER_VALIDATE_INT)) {
            $this->render('error.twig', ['message' => 'Identifiant de publication invalide']);
            return;
        }

        try {
            $post = $this->postsRepository->getPostById($postId);

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
                $success = $this->postsRepository->editPost((int)$postId, $title, $chapo, $content, $author, $userId);

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

    public function deletePost(int $postId): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        try {
            $success = $this->postsRepository->deletePost($postId);
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
