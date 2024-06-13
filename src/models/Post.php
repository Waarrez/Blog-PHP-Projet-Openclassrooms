<?php

namespace Root\P5\models;

use DateTime;

class Post
{
    // Identifiant du post
    private int $id;

    // Titre du post
    private string $title;

    // Chapô du post
    private string $chapo;

    // Contenu du post
    private string $content;

    // Auteur du post
    private string $author;

    // Identifiant de l'utilisateur associé au post
    private int $userId;

    // Date de la dernière mise à jour du post
    private DateTime $updatedAt;

    // Getters et setters pour l'identifiant du post
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // Getters et setters pour le titre du post
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    // Getters et setters pour le chapô du post
    public function getChapo(): string
    {
        return $this->chapo;
    }

    public function setChapo(string $chapo): void
    {
        $this->chapo = $chapo;
    }

    // Getters et setters pour le contenu du post
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    // Getters et setters pour l'auteur du post
    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    // Getters et setters pour l'identifiant de l'utilisateur associé au post
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    // Getters et setters pour la date de la dernière mise à jour du post
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
