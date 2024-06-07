<?php

namespace Root\P5\models;

use DateTime;
use Exception;
use Root\P5\Classes\DatabaseConnect;
use PDO;

class UsersRepository
{
    private DatabaseConnect $databaseConnect;

    public function __construct(DatabaseConnect $databaseConnect)
    {
        $this->databaseConnect = $databaseConnect;
    }

    /**
     * @throws Exception
     */
    private function fetchUsers($row): ?User
    {
        if (!$row) {
            return null;
        }

        $user = new User();
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setPassword($row['password']);
        $user->setIsConfirmed($row['isConfirmed']);
        $user->setRoles($row['roles']);
        $user->setCreatedAt(new DateTime($row['createdAt']));

        return $user;
    }

    /**
     * @throws Exception
     */
    public function createUser(string $username, string $email, string $password): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "Cet email est déjà utilisé, veuillez en choisir un autre.";
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $statement = $pdo->prepare("INSERT INTO user (username, email, password, isConfirmed , roles ,createdAt) VALUES (:username, :email, :password, false, 'USER' ,NOW())");
        $statement->bindValue(':username', $username, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':password', $hashed_password, PDO::PARAM_STR);
        $success = $statement->execute();

        return $success;
    }

    /**
     * @throws Exception
     */
    public function loginUser(string $email, string $password): ?User
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT * FROM user WHERE email = :email");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        if (!password_verify($password, $row['password'])) {
            return null;
        }

        return $this->fetchUsers($row);
    }

    /**
     * @throws Exception
     */
    public function getUserNotApprouved(): array
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("SELECT * FROM user WHERE isConfirmed = false");
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->fetchUsers($row);
        }

        return $users;
    }

    public function confirmUser(int $userId): bool
    {
        $pdo = $this->databaseConnect->getConnection();
        if ($pdo === null) {
            throw new \Exception('Erreur de connexion à la base de données');
        }

        $statement = $pdo->prepare("
            UPDATE user
            SET isConfirmed = true
            WHERE id = :userId
        ");

        $statement->execute([
            'userId' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }
}
