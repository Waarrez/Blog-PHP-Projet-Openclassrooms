<?php

namespace Root\P5\models;

use DateTime;

class Comment
{
    // Identifiant du commentaire
    private int $id;

    // Contenu du commentaire
    private string $content;

    // Identifiant du post auquel le commentaire est associé
    private int $postId;

    // Identifiant de l'utilisateur qui a créé le commentaire
    private int $userId;

    // Indique si le commentaire est confirmé ou non
    private bool $isConfirmed;

    // Nom d'utilisateur de l'auteur du commentaire
    private string $username;

    // Titre du post auquel le commentaire est associé
    private string $postTitle;

    // Getters et setters pour l'identifiant du commentaire
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // Getters et setters pour le contenu du commentaire
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    // Getters et setters pour l'identifiant du post associé au commentaire
    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    // Getters et setters pour l'identifiant de l'utilisateur qui a créé le commentaire
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    // Getters et setters pour le nom d'utilisateur de l'auteur du commentaire
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    // Getters et setters pour indiquer si le commentaire est confirmé ou non
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    // Getters et setters pour le titre du post associé au commentaire
    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    public function setPostTitle(string $postTitle): void
    {
        $this->postTitle = $postTitle;
    }
}
