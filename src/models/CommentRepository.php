<?php

namespace Root\P5\models;

use Exception;
use PDO;
use Root\P5\Manager\DatabaseConnect;

class CommentRepository
{
    public function __construct(private DatabaseConnect $databaseConnect)
    {
    }

    /**
     * @throws Exception
     */
    public function getPostIdBySlug(string $slug): ?int
    {
        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT id FROM post WHERE slug = :slug");
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? (int) $row['id'] : null;
    }

    /**
     * @return array<Comment>
     * @throws Exception
     */
    public function getCommentsByPost(string $slug): array
    {
        $postId = $this->getPostIdBySlug($slug);
        if ($postId === null) {
            throw new \Exception('Post non trouvé pour le slug donné');
        }

        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("
            SELECT c.*, u.username 
            FROM comment c
            JOIN user u ON c.user_id = u.id
            WHERE c.post_id = :postId
            AND c.isConfirmed = 1
        ");
        $statement->execute(['postId' => $postId]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @throws Exception
     */
    public function addComment(string $slug, string $content, int $userId): bool
    {
        $postId = $this->getPostIdBySlug($slug);
        if ($postId === null) {
            throw new \Exception('Post non trouvé pour le slug donné');
        }

        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("
            INSERT INTO comment (content, post_id, user_id, isConfirmed)
            VALUES (:content, :postId, :userId, 0)
        ");

        $statement->execute([
            'content' => $content,
            'postId' => $postId,
            'userId' => $userId
        ]);

        return $statement->rowCount() > 0;
    }

    /**
     * @return array<array<string, mixed>>
     * @throws Exception
     */
    public function getUnconfirmedComments(): array
    {
        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("
           SELECT c.*, u.username, p.title as postTitle
            FROM comment c
            JOIN user u ON c.user_id = u.id
            JOIN post p ON c.post_id = p.id
            WHERE c.isConfirmed = 0
        ");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function confirmComment(int $commentId): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if (!$pdo instanceof \PDO) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("
            UPDATE comment
            SET isConfirmed = true
            WHERE id = :commentId
        ");

        $statement->execute([
            'commentId' => $commentId,
        ]);

        return $statement->rowCount() > 0;
    }
}
