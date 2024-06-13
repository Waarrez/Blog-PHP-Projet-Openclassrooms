<?php

namespace Root\P5\models;

use DateTime;
use Exception;
use Root\P5\Classes\DatabaseConnect;
use PDO;

class PostsRepository
{
    private DatabaseConnect $databaseConnect;

    public function __construct(DatabaseConnect $databaseConnect)
    {
        $this->databaseConnect = $databaseConnect;
    }

    /**
     * @param array<string, mixed> $row
     * @throws Exception
     */
    private function fetchPost(array $row): ?Post
    {
        if (!$row) {
            return null;
        }

        $post = new Post();
        $post->setId($row['id']);
        $post->setTitle($row['title']);
        $post->setChapo($row['chapo']);
        $post->setContent($row['content']);
        $post->setAuthor($row['author']);
        $post->setUserId($row['user_id']);
        $post->setUpdatedAt(new DateTime($row['updatedAt']));

        return $post;
    }

    /**
     * @return array<Post|null>
     * @throws Exception
     */
    public function getPosts(): array
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT * FROM post ORDER BY updatedAt DESC");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->fetchPost($row);
        }

        return $posts;
    }

    /**
     * @return array<Post|null>
     * @throws Exception
     */
    public function getPostsByUser(int $userId): array
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT * FROM post WHERE user_id = :userId ORDER BY updatedAt DESC");
        $statement->execute(['userId' => $userId]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->fetchPost($row);
        }

        return $posts;
    }

    /**
     * @param mixed $postId
     * @return Post|null
     * @throws Exception
     */
    public function getPostById(mixed $postId): ?Post
    {
        if (!is_numeric($postId)) {
            throw new \InvalidArgumentException('L\'identifiant de publication doit être un entier.');
        }

        $postId = (int)$postId;

        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT * FROM post WHERE id = :id");
        $statement->bindValue(':id', $postId, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $this->fetchPost($row);
    }

    /**
     * @param string $title
     * @param string $chapo
     * @param string $content
     * @param int $userId
     * @param string $author
     * @return Post|null
     * @throws Exception
     */
    public function addPost(string $title, string $chapo, string $content, int $userId, string $author): ?Post
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("INSERT INTO post (title, chapo, content, author, updatedAt, user_id) VALUES (:title, :chapo, :content, :author, NOW(), :userId)");
        $statement->bindValue(':title', $title, PDO::PARAM_STR);
        $statement->bindValue(':chapo', $chapo, PDO::PARAM_STR);
        $statement->bindValue(':content', $content, PDO::PARAM_STR);
        $statement->bindValue(':author', $author, PDO::PARAM_STR);
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();
        $postId = $pdo->lastInsertId();
        return $this->getPostById($postId);
    }

    /**
     * @throws Exception
     */
    public function editPost(int $postId, string $title, string $chapo, string $content, string $author, int $userId): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("UPDATE post SET title = :title, chapo = :chapo, content = :content, author = :author, updatedAt = NOW(), user_id = :userId WHERE id = :postId");
        $statement->bindValue(':title', $title, PDO::PARAM_STR);
        $statement->bindValue(':chapo', $chapo, PDO::PARAM_STR);
        $statement->bindValue(':content', $content, PDO::PARAM_STR);
        $statement->bindValue(':author', $author, PDO::PARAM_STR);
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        return $statement->execute();
    }

    /**
     * @throws Exception
     */
    public function deletePost(int $postId): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("DELETE FROM post WHERE id = :postId");
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        return $statement->execute();
    }
}
