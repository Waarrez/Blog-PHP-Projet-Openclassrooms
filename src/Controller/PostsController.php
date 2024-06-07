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
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function dashboardPosts(): void
    {
        if (!isset($_SESSION["user_id"])) {
            header('Location: /login');
            exit();
        }

        $userConfirmed = $_SESSION["isConfirmed"];
        if (!$userConfirmed) {
            header('Location: /');
            exit();
        }

        try {
            $posts = $this->postsRepository->getPostsByUser($_SESSION["user_id"]);
            $this->render('posts/dashboard_post.twig', ['posts' => $posts]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
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
            $title = $_POST['title'];
            $chapo = $_POST['chapo'];
            $content = $_POST['content'];
            $author = $_SESSION['username'];
            $userId = $_SESSION['user_id'];

            if (!empty($title) && !empty($chapo) && !empty($content)) {
                $success = $this->postsRepository->addPost($title, $chapo, $content, $userId, $author);

                if ($success) {
                    header('Location: /dashboard_posts');
                } else {
                    echo "Erreur lors de la création du post";
                    exit();
                }
            } else {
                echo "Touts les champs doivent être complété";
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
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function addComment(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION["user_id"])) {
                header('Location: /login');
                exit();
            }

            $content = $_POST['content'];
            $userId = $_SESSION['user_id'];
            $postId = $_POST['post_id'];

            if (!empty($content)) {
                try {
                    $success = $this->commentsRepository->addComment($postId, $content, $userId);

                    if ($success) {
                        header("Location: /post/{$postId}");
                    } else {
                        echo "Erreur lors de l'ajout du commentaire";
                        exit();
                    }
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                }
            } else {
                echo "Le champ de commentaire ne peut pas être vide";
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
                echo 'Post not found';
                return;
            }

            if ($post->getUserId() !== $_SESSION['user_id']) {
                echo 'Vous n\'avez pas l\'autorisation de modifier ce post.';
                return;
            }

            $this->render('posts/edit_post.twig', ['post' => $post]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
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
            $title = $_POST['title'];
            $chapo = $_POST['chapo'];
            $content = $_POST['content'];
            $author = $_SESSION['username'];
            $userId = $_SESSION['user_id'];
            $postId = $_POST['postId'];

            if (!empty($title) && !empty($chapo) && !empty($content)) {
                $success = $this->postsRepository->editPost($postId, $title, $chapo, $content, $author, $userId);

                if ($success) {
                    header('Location: /dashboard_posts');
                } else {
                    echo "Erreur lors de la création du post";
                    exit();
                }
            } else {
                echo "Touts les champs doivent être complété";
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
                echo "Erreur lors de la suppression du post";
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
