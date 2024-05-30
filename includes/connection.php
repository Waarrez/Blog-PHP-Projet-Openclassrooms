<?php

require_once "../config/config.php";

$conn = new mysqli($servername, $username, $password, $database,3307);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}