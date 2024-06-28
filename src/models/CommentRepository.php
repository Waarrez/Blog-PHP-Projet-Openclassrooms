<?php

namespace Root\P5\models;

use Exception;
use PDO;
use Root\P5\Classes\DatabaseConnect;

class CommentRepository
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
    private function fetchComments(array $row): ?Comment
    {
        if (!$row) {
            return null;
        }

        $comment = new Comment();
        $comment->setCommentId($row['id']);
        $comment->setContent($row['content']);
        $comment->setPostId($row['post_id']);
        $comment->setUserId($row['user_id']);
        $comment->setUsername($row['username']);

        return $comment;
    }

    /**
     * @param string $slug
     * @return int|null
     * @throws Exception
     */
    public function getPostIdBySlug(string $slug): ?int
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT id FROM post WHERE slug = :slug");
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? (int) $row['id'] : null;
    }

    /**
     * @param string $slug
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
        if ($pdo === null) {
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
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $comments = [];
        foreach ($rows as $row) {
            $comments[] = $row;
        }

        return $comments;
    }


    /**
     * @param string $slug
     * @param string $content
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function addComment(string $slug, string $content, int $userId): bool
    {
        $postId = $this->getPostIdBySlug($slug);
        if ($postId === null) {
            throw new \Exception('Post non trouvé pour le slug donné');
        }

        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
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
        if ($pdo === null) {
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
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $comments = [];
        foreach ($rows as $row) {
            $comments[] = $row;
        }

        return $comments;
    }

    public function confirmComment(int $commentId): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
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
