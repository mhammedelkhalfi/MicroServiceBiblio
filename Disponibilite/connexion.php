<?php
// Paramètres de connexion à la base de données
$host = 'localhost';  // Hôte
$dbname = 'microserviceebook';  // Nom de la base de données
$username = 'root';  // Nom d'utilisateur
$password = '';  // Mot de passe

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Pour activer les exceptions en cas d'erreur
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
