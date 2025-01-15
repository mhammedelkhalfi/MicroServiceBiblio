<?php
require_once '../connexion.php';

// Vérifier si l'ID du livre est passé
if (isset($_GET['id'])) {
    $idLivre = $_GET['id'];

    // Préparer et exécuter la requête de suppression
    $stmt = $pdo->prepare("DELETE FROM livre WHERE idLivre = :idLivre");
    $stmt->bindValue(':idLivre', $idLivre);

    if ($stmt->execute()) {
        // Redirection vers la page d'index avec un message de succès
        header('Location: index.php?message=Livre supprimé avec succès');
        exit;
    } else {
        // Gérer l'erreur
        echo "Erreur lors de la suppression du livre.";
    }
} else {
    // Redirection en cas d'accès incorrect
    header('Location: index.php');
    exit;
}
?>
