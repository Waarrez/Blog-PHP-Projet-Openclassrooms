<?php

namespace Root\P5\models;

use DateTime;

class Comment
{
    /**
     * @var int L'identifiant du commentaire.
     */
    private int $commentId;

    /**
     * @var string Le contenu du commentaire.
     */
    private string $content;

    /**
     * @var int L'identifiant du post associé au commentaire.
     */
    private int $postId;

    /**
     * @var int L'identifiant de l'utilisateur qui a créé le commentaire.
     */
    private int $userId;

    /**
     * @var bool Indique si le commentaire est confirmé ou non.
     */
    private bool $isConfirmed;

    /**
     * @var string Le nom d'utilisateur de l'auteur du commentaire.
     */
    private string $username;

    /**
     * @var string Le titre du post associé au commentaire.
     */
    private string $postTitle;

    /**
     * Obtient l'identifiant du commentaire.
     *
     * @return int L'identifiant du commentaire.
     */
    public function getCommentId(): int
    {
        return $this->commentId;
    }

    /**
     * Définit l'identifiant du commentaire.
     *
     * @param int $commentId L'identifiant du commentaire.
     * @return void
     */
    public function setCommentId(int $commentId): void
    {
        $this->commentId = $commentId;
    }

    /**
     * Obtient le contenu du commentaire.
     *
     * @return string Le contenu du commentaire.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Définit le contenu du commentaire.
     *
     * @param string $content Le contenu du commentaire.
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Obtient l'identifiant du post associé au commentaire.
     *
     * @return int L'identifiant du post associé au commentaire.
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * Définit l'identifiant du post associé au commentaire.
     *
     * @param int $postId L'identifiant du post.
     * @return void
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    /**
     * Obtient l'identifiant de l'utilisateur qui a créé le commentaire.
     *
     * @return int L'identifiant de l'utilisateur.
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Définit l'identifiant de l'utilisateur qui a créé le commentaire.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Obtient le nom d'utilisateur de l'auteur du commentaire.
     *
     * @return string Le nom d'utilisateur de l'auteur du commentaire.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Définit le nom d'utilisateur de l'auteur du commentaire.
     *
     * @param string $username Le nom d'utilisateur.
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Indique si le commentaire est confirmé ou non.
     *
     * @return bool True si le commentaire est confirmé, sinon False.
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * Définit si le commentaire est confirmé ou non.
     *
     * @param bool $isConfirmed Indique si le commentaire est confirmé.
     * @return void
     */
    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * Obtient le titre du post associé au commentaire.
     *
     * @return string Le titre du post associé au commentaire.
     */
    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    /**
     * Définit le titre du post associé au commentaire.
     *
     * @param string $postTitle Le titre du post.
     * @return void
     */
    public function setPostTitle(string $postTitle): void
    {
        $this->postTitle = $postTitle;
    }
}
