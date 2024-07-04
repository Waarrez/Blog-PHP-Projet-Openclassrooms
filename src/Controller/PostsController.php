<?php

namespace Root\P5\Controller;

use Exception;
use Random\RandomException;
use Root\P5\Manager\DatabaseConnect;
use Root\P5\models\CommentRepository;
use Root\P5\models\PostsRepository;
use Root\P5\Services\CSRFService;
use Root\P5\Services\PostService;
use Twig\Environment;

class PostsController extends BaseController
{
    private PostService $postService;
    private CSRFService $CSRFService;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $postsRepository = new PostsRepository($db);
        $commentsRepository = new CommentRepository($db);
        $this->postService = new PostService($postsRepository, $commentsRepository);
        $this->CSRFService = new CSRFService();
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

    /**
     * @throws RandomException
     */
    public function addPost(): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        $csrfService = $this->CSRFService;
        $csrfToken = $csrfService->generateToken();

        $this->render('posts/add_post.twig', ['csrf_token' => $csrfToken]);
    }

    /**
     * @throws Exception
     */
    public function addPostForm(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!$this->isUserLoggedIn()) {
                $this->redirect('/login');
                return;
            }

            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->CSRFService->validateToken($csrfToken)) {
                $this->render('error.twig', ['message' => 'Invalid CSRF token']);
                return;
            }

            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $author = $_SESSION['username'] ?? '';
            $userId = $_SESSION['user_id'] ?? null;

            if (!empty($title) && !empty($chapo) && !empty($content) && $userId !== null) {
                try {
                    $success = $this->postService->addPost($title, $chapo, $content, $userId, $author);

                    if ($success) {
                        $_SESSION['success'] = 'Votre article a bien été ajouté';
                        $this->redirect('/dashboard_posts');
                    } else {
                        $this->render('error.twig', ['message' => 'Erreur lors de la création du post']);
                    }
                } catch (Exception $e) {
                    // Log the exception or handle it accordingly
                    $this->render('error.twig', ['message' => 'Une erreur inattendue est survenue']);
                }
            } else {
                $this->render('error.twig', ['message' => 'Tous les champs doivent être complétés']);
            }
        } else {
            $this->redirect('/add_post');
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

    /**
     * @throws RandomException
     */
    public function editPost(string $slug): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
        }

        $csrfService = $this->CSRFService;
        $csrfToken = $csrfService->generateToken();

        if ($slug === '' || $slug === '0') {
            $this->render('error.twig', ['message' => 'Slug de publication invalide']);
            return;
        }

        try {
            $post = $this->postService->getPostBySlug($slug);

            if (trim(strval($post->getUserId())) !== trim(strval($_SESSION['user_id']))) {
                $this->render('error.twig', ['message' => 'Vous n\'avez pas l\'autorisation de modifier cet article.']);
                return;
            }

            $this->render('posts/edit_post.twig', ['post' => $post, 'csrf_token' => $csrfToken]);
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
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->CSRFService->validateToken($csrfToken)) {
                $this->render('error.twig', ['message' => 'Invalid CSRF token']);
                return;
            }

            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $author = $_SESSION['username'] ?? '';
            $userId = $_SESSION['user_id'] ?? null;
            $postId = filter_input(INPUT_POST, 'postId', FILTER_VALIDATE_INT);

            if (!empty($title) && !empty($chapo) && !empty($content) && $userId !== null && $postId !== false) {
                try {
                    $success = $this->postService->editPost((int)$postId, $title, $chapo, $content, $author, $userId);

                    if ($success) {
                        $_SESSION['success'] = 'Votre article a bien été modifié';
                        header('Location: /dashboard_posts');
                        exit;
                    } else {
                        $this->render('error.twig', ['message' => 'Erreur lors de la modification de l\'article']);
                    }
                } catch (Exception $e) {
                    $this->render('error.twig', ['message' => 'Une erreur inattendue est survenue']);
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
