<?php
include 'connexion.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $adressemail = $_POST['adressemail'];
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT); // Hash the password
    $role = 'UTILISATEUR'; // Default role for new users
    $status = 'AUTHORIZED'; // Default status for new users

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, adressemail, motdepasse, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $adressemail, $motdepasse, $role, $status]);

    $success_message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Inscription</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
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
                            <label for="adressemail" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control" id="adressemail" name="adressemail" required>
                        </div>
                        <div class="mb-3">
                            <label for="motdepasse" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                    </form>
                    <p class="text-center mt-3">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
