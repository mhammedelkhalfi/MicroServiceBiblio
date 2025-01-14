<?php
require_once '../connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $prix = $_POST['prix'];
    $choix = $_POST['choix'];

    // Validation de l'entrée utilisateur
    if (empty($titre) || empty($auteur) || empty($prix) || empty($choix)) {
        die("Veuillez remplir tous les champs obligatoires.");
    }

    try {
        // Insérer le livre dans la table `livre`
        $stmt = $pdo->prepare("INSERT INTO livre (titre, auteur, type, disponibilite) VALUES (:titre, :auteur, 'location', 1)");
        $stmt->execute([':titre' => $titre, ':auteur' => $auteur]);
        $idLivre = $pdo->lastInsertId();

        // Définir la date actuelle pour les calculs
        $date_emprunt = date('Y-m-d'); // Date du système

        // Insérer les détails spécifiques dans la table `livre_de_location`
        if ($choix === 'duree') {
            $duree = $_POST['duree'];
            if (empty($duree)) {
                die("La durée est obligatoire si vous choisissez l'option 'Durée'.");
            }

            // Calcul de la date de retour
            $date_retour = date('Y-m-d', strtotime("+$duree days"));

            // Insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO livre_de_location (idLivre, prix, duree, date_emprunt, date_retour) VALUES (:idLivre, :prix, :duree, :date_emprunt, :date_retour)");
            $stmt->execute([
                ':idLivre' => $idLivre,
                ':prix' => $prix,
                ':duree' => $duree,
                ':date_emprunt' => $date_emprunt,
                ':date_retour' => $date_retour,
            ]);
        } elseif ($choix === 'dates') {
            $date_emprunt = $_POST['date_emprunt'];
            $date_retour = $_POST['date_retour'];
            if (empty($date_emprunt) || empty($date_retour)) {
                die("Les dates d'emprunt et de retour sont obligatoires si vous choisissez l'option 'Dates'.");
            }

            // Insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO livre_de_location (idLivre, prix, duree, date_emprunt, date_retour) VALUES (:idLivre, :prix, NULL, :date_emprunt, :date_retour)");
            $stmt->execute([
                ':idLivre' => $idLivre,
                ':prix' => $prix,
                ':date_emprunt' => $date_emprunt,
                ':date_retour' => $date_retour,
            ]);
        }

        header("Location: index.php?success=added");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de l'ajout du livre : " . $e->getMessage());
    }
}
?>
