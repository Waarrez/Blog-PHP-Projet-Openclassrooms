<?php

namespace Root\P5\Manager;

use PDO;
use PDOException;

class DatabaseConnect
{
    /**
     * @var string Database user.
     */
    private string $user = "root";

    /**
     * @var string Database password.
     */
    private string $password = "";

    /**
     * @var PDO|null Database connection.
     */
    private ?PDO $connect = null;

    /**
     * DatabaseConnect constructor.
     */
    public function __construct()
    {
        $this->connection();
    }

    /**
     * Establishes a database connection.
     */
    public function connection(): void
    {
        try {
            $dsn = "mysql:host=localhost;port=3307;dbname=p5;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
            ];
            $this->connect = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            error_log('Error: ' . $e->getMessage());
        }
    }

    /**
     * Closes the database connection.
     */
    public function disconnect(): void
    {
        $this->connect = null;
    }

    /**
     * Returns the database connection.
     *
     * @return PDO|null Database connection.
     */
    public function getConnection(): ?PDO
    {
        return $this->connect;
    }
}
