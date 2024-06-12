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
            exit();
        }

        $userConfirmed = $_SESSION["isConfirmed"] ?? false;
        if (!$userConfirmed) {
            header('Location: /');
            exit();
        }

        try {
            $userId = $_SESSION["user_id"];
            $posts = $this->postsRepository->getPostsByUser($userId);
            $this->render('posts/dashboard_post.twig', ['posts' => $posts]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->render('error.twig', ['message' => 'Une erreur s\'est produite lors de la récupération des articles du tableau de bord.']);
        }
    }

    public function addPost(): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
            exit();
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
                exit();
            }

            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $author = $_SESSION['username'] ?? '';
            $userId = $_SESSION['user_id'];

            if (!empty($title) && !empty($chapo) && !empty($content)) {
                $success = $this->postsRepository->addPost($title, $chapo, $content, $userId, $author);

                if ($success) {
                    header('Location: /dashboard_posts');
                    exit();
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
                exit();
            }

            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $userId = $_SESSION['user_id'];
            $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

            if (!empty($content)) {
                try {
                    $success = $this->commentsRepository->addComment($postId, $content, $userId);

                    if ($success) {
                        header("Location: /post/{$postId}");
                        exit();
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
            exit();
        }

        try {
            $post = $this->postsRepository->getPostById($postId);

            if ($post === null) {
                $this->render('error.twig', ['message' => 'Article non trouvé']);
                return;
            }

            if ($post->getUserId() !== $_SESSION['user_id']) {
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
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $author = $_SESSION['username'] ?? '';
            $userId = $_SESSION['user_id'];
            $postId = filter_input(INPUT_POST, 'postId', FILTER_VALIDATE_INT);

            if (!empty($title) && !empty($chapo) && !empty($content)) {
                $success = $this->postsRepository->editPost($postId, $title, $chapo, $content, $author, $userId);

                if ($success) {
                    header('Location: /dashboard_posts');
                    exit();
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
            exit();
        }

        try {
            $success = $this->postsRepository->deletePost($postId);
            if ($success) {
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