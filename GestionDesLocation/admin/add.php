<?php
require_once '../connexion.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $image = $_POST['image']; // Assuming you're capturing the image URL or path
    $disponibilite = $_POST['disponibilite']; // Assuming you have a field for availability

    // Préparer et exécuter la requête d'insertion
    $stmt = $pdo->prepare("INSERT INTO livre (titre, auteur, image, disponibilite, type) VALUES (:titre, :auteur, :image, :disponibilite, 'location')");
    $stmt->bindValue(':titre', $titre);
    $stmt->bindValue(':auteur', $auteur);
    $stmt->bindValue(':image', $image);
    $stmt->bindValue(':disponibilite', $disponibilite);

    if ($stmt->execute()) {
        // Redirection vers la page d'index avec un message de succès
        header('Location: index.php?message=Livre ajouté avec succès');
        exit;
    } else {
        // Gérer l'erreur
        echo "Erreur lors de l'ajout du livre.";
    }
} else {
    // Redirection en cas d'accès incorrect
    header('Location: index.php');
    exit;
}
?>
