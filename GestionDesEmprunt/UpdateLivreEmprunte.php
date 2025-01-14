<?php
require 'connexion.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUtilisateur = $_POST['utilisateur_id'];
    $idLivre = $_POST['livre_id'];
    $dateRetour = $_POST['pret_date_retour'];
    $duree = $_POST['pret_duree'];

    // Requête pour mettre à jour la durée et la date de retour dans la base de données
    $sqlUpdate = "
        UPDATE livre_pret
        SET 
            duree = :duree, 
            date_retour = :date_retour
        WHERE idLivre = :idLivre
    ";

    try {
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':duree', $duree, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':date_retour', $dateRetour);
        $stmtUpdate->bindParam(':idLivre', $idLivre, PDO::PARAM_INT);
        $stmtUpdate->execute();

        echo "Emprunt mis à jour avec succès!";
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour : " . $e->getMessage());
    }
}
?>
