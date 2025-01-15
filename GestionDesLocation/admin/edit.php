<?php
require_once '../connexion.php';

// Vérifier si l'ID du livre est passé
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idLivre'])) {
    $idLivre = $_POST['idLivre'];
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $image = $_POST['image']; // Assuming you want to update the image field too
    $disponibilite = $_POST['disponibilite']; // Assuming you have a field for availability

    // Préparer et exécuter la requête de mise à jour
    $stmt = $pdo->prepare("UPDATE livre SET titre = :titre, auteur = :auteur, image = :image, disponibilite = :disponibilite, type = 'location' WHERE idLivre = :idLivre");
    $stmt->bindValue(':titre', $titre);
    $stmt->bindValue(':auteur', $auteur);
    $stmt->bindValue(':image', $image);
    $stmt->bindValue(':disponibilite', $disponibilite);
    $stmt->bindValue(':idLivre', $idLivre);

    if ($stmt->execute()) {
        // Redirection vers la page d'index avec un message de succès
        header('Location: index.php?message=Livre modifié avec succès');
        exit;
    } else {
        // Gérer l'erreur
        echo "Erreur lors de la mise à jour du livre.";
    }
} else {
    // Redirection en cas d'accès incorrect
    header('Location: index.php');
    exit;
}
?>
