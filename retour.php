<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

// Vérification des paramètres
if (!isset($_GET['id']) || !isset($_GET['user_id']) || !is_numeric($_GET['id']) || !is_numeric($_GET['user_id'])) {
    die("Paramètres invalides.");
}

$idLivre = $_GET['id'];
$user_id = $_GET['user_id'];

// Set the MySQL session time zone to 'SYSTEM' (or '+01:00' for CET or '+02:00' for CEST)
$pdo->exec("SET time_zone = 'SYSTEM'");

// Update the availability of the book
$sql_livre = "UPDATE livre 
              SET disponibilite = 1 
              WHERE idLivre = :idLivre AND idUtilisateur = :user_id";
$stmt_livre = $pdo->prepare($sql_livre);
$stmt_livre->execute(['idLivre' => $idLivre, 'user_id' => $user_id]);

// Update the return date in livre_pret
$sql_livre_pret = "UPDATE livre_pret lp
                   JOIN livre l ON lp.idLivre = l.idLivre
                   SET lp.date_retour = CURDATE()
                   WHERE l.idUtilisateur = :user_id AND l.idLivre = :idLivre";
$stmt_livre_pret = $pdo->prepare($sql_livre_pret);
$stmt_livre_pret->execute(['idLivre' => $idLivre, 'user_id' => $user_id]);

// Set success message in session
$_SESSION['success_message'] = "Retour effectué avec succès !";

// Redirection vers l'historique des emprunts
header("Location: historique-emprunts.php?id=" . $user_id); 
exit;
?>
