<?php

namespace Root\P5\Services;

use Exception;
use Root\P5\models\CommentRepository;
use Root\P5\models\Post;
use Root\P5\models\PostsRepository;

class PostService
{
    public function __construct(private readonly PostsRepository $postsRepository, private readonly CommentRepository $commentsRepository)
    {
    }

    /**
     * Get all posts
     *
     * @throws Exception
     */
    public function getPosts(): array
    {
        return $this->postsRepository->getPosts();
    }

    /**
     * Get posts by user ID
     *
     * @throws Exception
     */
    public function getPostsByUser(int $userId): array
    {
        return $this->postsRepository->getPostsByUser($userId);
    }

    /**
     * Add a new post
     *
     * @throws Exception
     */
    public function addPost(string $title, string $chapo, string $content, int $userId, string $author): Post
    {
        return $this->postsRepository->addPost($title, $chapo, $content, $userId, $author);
    }

    /**
     * Get post by ID
     *
     * @throws Exception
     */
    public function getPostBySlug(string $slug): Post
    {
        $post = $this->postsRepository->getPostBySlug($slug);

        if (!$post instanceof \Root\P5\models\Post) {
            error_log("L'article avec le slug $slug n'a pas été trouvé.");
        }

        return $post;
    }

    /**
     * Get comments by post ID
     *
     * @throws Exception
     */
    public function getCommentsByPost(string $slug): array
    {
        return $this->commentsRepository->getCommentsByPost($slug);
    }

    /**
     * Add a new comment
     *
     * @throws Exception
     */
    public function addComment(string $slug, string $content, int $userId): bool
    {
        return $this->commentsRepository->addComment($slug, $content, $userId);
    }

    /**
     * Edit a post
     *
     * @throws Exception
     */
    public function editPost(int $postId, string $title, string $chapo, string $content, string $author, int $userId): bool
    {
        return $this->postsRepository->editPost($postId, $title, $chapo, $content, $author, $userId);
    }

    /**
     * Delete a post
     *
     * @throws Exception
     */
    public function deletePost(string $slug): bool
    {
        return $this->postsRepository->deletePost($slug);
    }
}
