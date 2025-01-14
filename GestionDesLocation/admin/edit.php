<?php
require_once '../connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $idLivre = $_POST['idLivre'];
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $prix = $_POST['prix'];
    $dateEmprunt = $_POST['date_emprunt'];
    $dateRetour = $_POST['date_retour'];

    // Calcul de la durée
    $date1 = new DateTime($dateEmprunt);
    $date2 = new DateTime($dateRetour);
    $duree = $date1->diff($date2)->days;

    // Mise à jour des informations dans la base de données
    $query = "
        UPDATE livre l
        INNER JOIN livre_de_location ll ON l.idLivre = ll.idLivre
        SET l.titre = :titre, l.auteur = :auteur, ll.prix = :prix, 
            ll.date_emprunt = :date_emprunt, ll.date_retour = :date_retour, ll.duree = :duree
        WHERE l.idLivre = :idLivre
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':titre', $titre);
    $stmt->bindValue(':auteur', $auteur);
    $stmt->bindValue(':prix', $prix);
    $stmt->bindValue(':date_emprunt', $dateEmprunt);
    $stmt->bindValue(':date_retour', $dateRetour);
    $stmt->bindValue(':duree', $duree);
    $stmt->bindValue(':idLivre', $idLivre);

    if ($stmt->execute()) {
        // Redirection après mise à jour
        header('Location: index.php?message=Mise à jour réussie');
        exit;
    } else {
        echo "Erreur lors de la mise à jour : " . implode(", ", $stmt->errorInfo());
    }
}
