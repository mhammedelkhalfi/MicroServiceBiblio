<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "microserviceebook");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $conn->real_escape_string($_POST['nom']);
    $prenom = $conn->real_escape_string($_POST['prenom']);
    $adressemail = $conn->real_escape_string($_POST['adressemail']);
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_BCRYPT);
    $role = $conn->real_escape_string($_POST['role']);

    // Validation du rôle
    if (!in_array($role, ['ADMIN', 'UTILISATEUR'])) {
        die("Rôle invalide.");
    }

    // Insertion dans la table utilisateur
    $sql = "INSERT INTO utilisateur (nom, prenom, adressemail, motdepasse, role) 
            VALUES ('$nom', '$prenom', '$adressemail', '$motdepasse', '$role')";
    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
        exit();
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Créer un compte</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>
        <div class="mb-3">
            <label for="adressemail" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="adressemail" name="adressemail" required>
        </div>
        <div class="mb-3">
            <label for="motdepasse" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <select class="form-control" id="role" name="role" required>
                <option value="UTILISATEUR">Utilisateur</option>
                <option value="ADMIN">Administrateur</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
    </form>
    <p class="text-center mt-3">Déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
</div>
</body>
</html>
