<?php

namespace Root\P5\Classes;

use PDO;
use PDOException;

class DatabaseConnect {

    private string $server = "localhost";
    private string $user = "root";
    private string $password = "";
    private string $bdd = "p5";
    private string $port = "3307";
    private ?PDO $connect = null;

    public function __construct()
    {
        $this->connection();
    }

    public function connection(): void
    {
        try {
            $dsn = "mysql:host=$this->server;port=$this->port;dbname=$this->bdd;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
            ];
            $this->connect = new PDO($dsn, $this->user, $this->password, $options);
        } catch(PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }

    public function disconnect(): void
    {
        $this->connect = null;
    }

    public function getConnection(): ?PDO
    {
        return $this->connect;
    }
}
