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

    // Getters et Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }
    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    public function setPostTitle(string $postTitle): void
    {
        $this->postTitle = $postTitle;
    }
}
