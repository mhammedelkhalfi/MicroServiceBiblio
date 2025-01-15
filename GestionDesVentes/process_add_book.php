<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "microserviceebook");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $titre = $conn->real_escape_string($_POST['titre']);
    $auteur = $conn->real_escape_string($_POST['auteur']);
    $type = $conn->real_escape_string($_POST['type']);
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;
    $prix = isset($_POST['prix']) ? (float) $_POST['prix'] : 0; // Assurez-vous que le prix est défini

    // Gestion de l'image
    $imageName = $_FILES['image']['name'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $uploadDir = "uploads/";
    $imagePath = $uploadDir . uniqid() . "_" . $imageName;

    if (move_uploaded_file($imageTmpName, $imagePath)) {
        // Insertion dans la table `livre`
        $sqlLivre = "INSERT INTO livre (titre, auteur, image, disponibilite, type) 
                     VALUES ('$titre', '$auteur', '$imagePath', $disponibilite, '$type')";

        if ($conn->query($sqlLivre) === TRUE) {
            // Récupération de l'ID du livre inséré
            $idLivre = $conn->insert_id;

            // Insérer dans la table `livre_de_vente` si le type est "vendre"
            if ($type === 'vendre') {
                $sqlVente = "INSERT INTO livre_de_vente (idLivre, prix) VALUES ($idLivre, $prix)";
                if (!$conn->query($sqlVente)) {
                    echo "Erreur lors de l'insertion dans livre_de_vente : " . $conn->error;
                }
            }

            // Rediriger vers la page de succès
            header("Location: success.php");
            exit();
        } else {
            echo "Erreur lors de l'insertion dans la table livre : " . $conn->error;
        }
    } else {
        echo "Erreur lors du téléchargement de l'image.";
    }
}

$conn->close();
?>
