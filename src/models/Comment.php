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

    /**
     * Obtient l'identifiant du commentaire.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Définit l'identifiant du commentaire.
     *
     * @param int $id Identifiant du commentaire
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Obtient le contenu du commentaire.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Définit le contenu du commentaire.
     *
     * @param string $content Contenu du commentaire
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Obtient l'identifiant du post associé au commentaire.
     *
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * Définit l'identifiant du post associé au commentaire.
     *
     * @param int $postId Identifiant du post
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    /**
     * Obtient l'identifiant de l'utilisateur qui a créé le commentaire.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Définit l'identifiant de l'utilisateur qui a créé le commentaire.
     *
     * @param int $userId Identifiant de l'utilisateur
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Obtient le nom d'utilisateur de l'auteur du commentaire.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Définit le nom d'utilisateur de l'auteur du commentaire.
     *
     * @param string $username Nom d'utilisateur
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Indique si le commentaire est confirmé ou non.
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * Définit si le commentaire est confirmé ou non.
     *
     * @param bool $isConfirmed Indique si le commentaire est confirmé
     */
    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * Obtient le titre du post associé au commentaire.
     *
     * @return string
     */
    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    /**
     * Définit le titre du post associé au commentaire.
     *
     * @param string $postTitle Titre du post
     */
    public function setPostTitle(string $postTitle): void
    {
        $this->postTitle = $postTitle;
    }
}
