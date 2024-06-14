<?php

namespace Root\P5\models;

use DateTime;

class Comment
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var int
     */
    private int $postId;

    /**
     * @var int
     */
    private int $userId;

    /**
     * @var bool
     */
    private bool $isConfirmed;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private string $postTitle;

    /**
     * @return int
     */
    public function getCommentId(): int
    {
        return $this->id;
    }

    /**
     * @param int $commentId
     * @return void
     */
    public function setCommentId(int $commentId): void
    {
        $this->id = $commentId;
    }

    /**
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     *
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     *
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     *
     * @param int $postId
     * @return void
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    /**
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     *
     * @param int $userId
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     *
     * @param string $username
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * @param bool $isConfirmed
     * @return void
     */
    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * @return string
     */
    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    /**
     * @param string $postTitle
     * @return void
     */
    public function setPostTitle(string $postTitle): void
    {
        $this->postTitle = $postTitle;
    }
}
