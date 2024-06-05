<?php

namespace Root\P5\Controller;

use Exception;
use Root\P5\Classes\DatabaseConnect;
use Root\P5\models\PostsRepository;
use Twig\Environment;

class PostsController extends BaseController
{
    private PostsRepository $postsRepository;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        parent::__construct($twig, $db);
        $this->postsRepository = new PostsRepository($db);
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

    /**
     * @throws Exception
     */
    public function viewPost(int $postId): void
    {
        try {
            $post = $this->postsRepository->getPostById($postId);

            if ($post === null) {
                echo 'Post not found';
                return;
            }

            $this->render('posts/view_post.twig', ['post' => $post]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
