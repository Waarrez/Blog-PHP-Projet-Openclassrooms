<?php

namespace Root\P5\Services;

use Exception;
use Root\P5\models\CommentRepository;
use Root\P5\models\Post;
use Root\P5\models\PostsRepository;

class PostService
{
    private PostsRepository $postsRepository;
    private CommentRepository $commentsRepository;

    public function __construct(PostsRepository $postsRepository, CommentRepository $commentsRepository)
    {
        $this->postsRepository = $postsRepository;
        $this->commentsRepository = $commentsRepository;
    }

    /**
     * Get all posts
     *
     * @throws Exception
     * @return array
     */
    public function getPosts(): array
    {
        return $this->postsRepository->getPosts();
    }

    /**
     * Get posts by user ID
     *
     * @param int $userId
     * @throws Exception
     * @return array
     */
    public function getPostsByUser(int $userId): array
    {
        return $this->postsRepository->getPostsByUser($userId);
    }

    /**
     * Add a new post
     *
     * @param string $title
     * @param string $chapo
     * @param string $content
     * @param int $userId
     * @param string $author
     * @return Post
     * @throws Exception
     */
    public function addPost(string $title, string $chapo, string $content, int $userId, string $author): Post
    {
        return $this->postsRepository->addPost($title, $chapo, $content, $userId, $author);
    }

    /**
     * Get post by ID
     *
     * @param string $slug
     * @return Post
     * @throws Exception
     */
    public function getPostBySlug(string $slug): Post
    {
        $post = $this->postsRepository->getPostBySlug($slug);

        if (!$post) {
            error_log("L'article avec le slug $slug n'a pas été trouvé.");
        }

        return $post;
    }

    /**
     * Get comments by post ID
     *
     * @param string $slug
     * @throws Exception
     * @return array
     */
    public function getCommentsByPost(string $slug): array
    {
        return $this->commentsRepository->getCommentsByPost($slug);
    }

    /**
     * Add a new comment
     *
     * @param string $slug
     * @param string $content
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function addComment(string $slug, string $content, int $userId): bool
    {
        return $this->commentsRepository->addComment($slug, $content, $userId);
    }

    /**
     * Edit a post
     *
     * @param int $postId
     * @param string $title
     * @param string $chapo
     * @param string $content
     * @param string $author
     * @param int $userId
     * @throws Exception
     * @return bool
     */
    public function editPost(int $postId, string $title, string $chapo, string $content, string $author, int $userId): bool
    {
        return $this->postsRepository->editPost($postId, $title, $chapo, $content, $author, $userId);
    }

    /**
     * Delete a post
     *
     * @param string $slug
     * @throws Exception
     * @return bool
     */
    public function deletePost(string $slug): bool
    {
        return $this->postsRepository->deletePost($slug);
    }
}
