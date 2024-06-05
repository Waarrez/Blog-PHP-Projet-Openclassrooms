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
     * @throws Exception
     */
    private function fetchPost($row): ?Post
    {
        if (!$row) {
            return null;
        }

        $post = new Post();
        $post->id = $row['id'];
        $post->title = $row['title'];
        $post->chapo = $row['chapo'];
        $post->content = $row['content'];
        $post->author = $row['author'];
        $post->updatedAt = new DateTime($row['updatedAt']);

        return $post;
    }

    /**
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
     * @throws Exception
     */
    public function getPostById(int $postId): ?Post
    {
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
}
