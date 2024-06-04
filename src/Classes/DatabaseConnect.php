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

    public function connection(): void
    {
        try {
            $this->connect = new PDO("mysql:host=$this->server;port=$this->port;dbname=$this->bdd", $this->user, $this->password);

            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connexion à la base de données réussie !";
        } catch(PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }

    public function disconnect(): void
    {
        $this->connect = null;
        echo "Déconnexion de la base de données réussie !";
    }

    public function getConnection(): ?PDO
    {
        return $this->connect;
    }
}
