<?php

namespace Root\P5\models;

use DateTime;

class Comment
{
    private int $id;

    private string $content;

    private int $postId;

    private int $userId;

    private bool $isConfirmed;

    private string $username;

    private string $postTitle;

    public function getCommentId(): int
    {
        return $this->id;
    }

    public function setCommentId(int $commentId): void
    {
        $this->id = $commentId;
    }

    
    public function getContent(): string
    {
        return $this->content;
    }

    
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    
    public function getPostId(): int
    {
        return $this->postId;
    }

    
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    
    public function getUserId(): int
    {
        return $this->userId;
    }

    
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    
    public function getUsername(): string
    {
        return $this->username;
    }

    
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    public function setPostTitle(string $postTitle): void
    {
        $this->postTitle = $postTitle;
    }
}
