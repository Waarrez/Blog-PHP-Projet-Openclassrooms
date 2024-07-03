<?php

namespace Root\P5\models;

use DateTime;
use Exception;
use Root\P5\Manager\DatabaseConnect;
use PDO;
use Root\P5\Services\SlugService;

class PostsRepository
{
    private SlugService $slugService;

    public function __construct(private DatabaseConnect $databaseConnect)
    {
        $this->slugService = new SlugService();
    }

    /**
     * @param array<string, mixed> $row
     * @throws Exception
     */
    private function fetchPost(array $row): ?Post
    {
        if ($row === []) {
            return null;
        }

        $post = new Post();
        $post->setId($row['id']);
        $post->setTitle($row['title']);
        $post->setChapo($row['chapo']);
        $post->setContent($row['content']);
        $post->setAuthor($row['author']);
        $post->setSlug($row['slug']);
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
        if (!$pdo instanceof \PDO) {
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
        if (!$pdo instanceof \PDO) {
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
     * @return Post|null
     * @throws Exception
     */
    public function getPostBySlug(string $slug): ?Post
    {
        if ($slug === '' || $slug === '0') {
            throw new \InvalidArgumentException('Le slug de publication doit être une chaîne de caractères non vide.');
        }

        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT * FROM post WHERE slug = :slug");
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

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
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        // Décodage des entités HTML
        $decodedTitle = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $decodedChapo = html_entity_decode($chapo, ENT_QUOTES, 'UTF-8');
        $decodedContent = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        $decodedAuthor = html_entity_decode($author, ENT_QUOTES, 'UTF-8');

        $slug = $this->slugService->generateSlug($decodedTitle);

        $statement = $pdo->prepare("INSERT INTO post (title, chapo, content, author, slug, updatedAt, user_id) VALUES (:title, :chapo, :content, :author, :slug, NOW(), :userId)");
        $statement->bindValue(':title', $decodedTitle, PDO::PARAM_STR);
        $statement->bindValue(':chapo', $decodedChapo, PDO::PARAM_STR);
        $statement->bindValue(':content', $decodedContent, PDO::PARAM_STR);
        $statement->bindValue(':author', $decodedAuthor, PDO::PARAM_STR);
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $this->getPostBySlug($slug);
    }

    /**
     * @throws Exception
     */
    public function editPost(int $postId, string $title, string $chapo, string $content, string $author, int $userId): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        // Décodage des entités HTML
        $decodedTitle = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $decodedChapo = html_entity_decode($chapo, ENT_QUOTES, 'UTF-8');
        $decodedContent = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        $decodedAuthor = html_entity_decode($author, ENT_QUOTES, 'UTF-8');

        $statement = $pdo->prepare("UPDATE post SET title = :title, chapo = :chapo, content = :content, author = :author, updatedAt = NOW(), user_id = :userId WHERE id = :postId");
        $statement->bindValue(':title', $decodedTitle, PDO::PARAM_STR);
        $statement->bindValue(':chapo', $decodedChapo, PDO::PARAM_STR);
        $statement->bindValue(':content', $decodedContent, PDO::PARAM_STR);
        $statement->bindValue(':author', $decodedAuthor, PDO::PARAM_STR);
        $statement->bindValue(':userId', $userId, PDO::PARAM_INT);
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * @throws Exception
     */
    public function deletePost(string $slug): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("DELETE FROM post WHERE slug = :slug");
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);
        return $statement->execute();
    }
}
