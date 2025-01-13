<?php
session_start();
require 'connexion.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifiez si un ID de livre est fourni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idLivre']) && isset($_POST['user_id'])) {
    $idLivre = $_POST['idLivre'];
    $user_id = $_POST['user_id'];
    $duree = 14; // Exemple : durée par défaut de l'emprunt en jours
    $date_emprunt = date('Y-m-d');
    $date_retour = date('Y-m-d', strtotime("+$duree days"));

    try {
        // Insérer dans la table `livre_pret`
        $stmt = $pdo->prepare("INSERT INTO livre_pret (idLivre, duree, date_emprunt, date_retour) VALUES (:idLivre, :duree, :date_emprunt, :date_retour)");
        $stmt->execute([
            ':idLivre' => $idLivre,
            ':duree' => $duree,
            ':date_emprunt' => $date_emprunt,
            ':date_retour' => $date_retour,
        ]);
    
        // Mettre à jour la table livre pour associer l'utilisateur au livre emprunté
        $stmt = $pdo->prepare("UPDATE livre SET idUtilisateur = :user_id, disponibilite = 0 WHERE idLivre = :idLivre");
        $stmt->execute([
            ':user_id' => $user_id,
            ':idLivre' => $idLivre
        ]);
    
        // Rediriger vers la page avec succès
        header("Location: userDashboard.php?success=1");

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
    
}
?>
