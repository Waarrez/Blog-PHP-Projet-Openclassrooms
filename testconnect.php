<?php

// Inclure le fichier contenant la classe DatabaseConnect
require_once './src/Classes/DatabaseConnect.php';

use Root\P5\Classes\DatabaseConnect;

// Créer une instance de la classe DatabaseConnect
$dbConnect = new DatabaseConnect();

// Appeler la méthode pour se connecter à la base de données
$dbConnect->connection();

// Si nécessaire, vous pouvez exécuter des requêtes ici pour tester la connexion

// Appeler la méthode pour se déconnecter de la base de données
$dbConnect->disconnect();

?>
