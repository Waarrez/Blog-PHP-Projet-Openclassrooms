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
     * @throws Exception
     */
    private function fetchComments($row): ?Comment
    {
        if (!$row) {
            return null;
        }

        $comment = new Comment();
        $comment->setId($row['id']);
        $comment->setContent($row['content']);
        $comment->setPostId($row['post_id']);
        $comment->setUserId($row['user_id']);
        $comment->setUsername($row['username']);

        return $comment;
    }

    /**
     * @throws Exception
     */
    public function getCommentsByPost(int $postId): array
    {
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
            $comments[] = $this->fetchComments($row);
        }

        return $comments;
    }

    public function addComment(int $postId, string $content, int $userId): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("
        INSERT INTO comment (content, post_id, user_id, isConfirmed)
        VALUES (:content, :postId, :userId, false)
        ");

        $statement->execute([
            'content' => $content,
            'postId' => $postId,
            'userId' => $userId
        ]);

        return $statement->rowCount() > 0;
    }
}
