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
     * @param int $postId
     * @throws Exception
     * @return mixed
     */
    public function getPostById(int $postId)
    {
        return $this->postsRepository->getPostById($postId);
    }

    /**
     * Get comments by post ID
     *
     * @param int $postId
     * @throws Exception
     * @return array
     */
    public function getCommentsByPost(int $postId): array
    {
        return $this->commentsRepository->getCommentsByPost($postId);
    }

    /**
     * Add a new comment
     *
     * @param int $postId
     * @param string $content
     * @param int $userId
     * @throws Exception
     * @return bool
     */
    public function addComment(int $postId, string $content, int $userId): bool
    {
        return $this->commentsRepository->addComment($postId, $content, $userId);
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
     * @param int $postId
     * @throws Exception
     * @return bool
     */
    public function deletePost(int $postId): bool
    {
        return $this->postsRepository->deletePost($postId);
    }
}
