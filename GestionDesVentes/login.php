<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "microserviceebook");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adressemail = $conn->real_escape_string($_POST['adressemail']);
    $motdepasse = $_POST['motdepasse'];

    // Recherche de l'utilisateur dans la base de données
    $sql = "SELECT * FROM utilisateur WHERE adressemail='$adressemail'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($motdepasse, $user['motdepasse'])) {
            // Connexion réussie
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nom'] . " " . $user['prenom'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Adresse email non trouvée.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Se connecter</h2>
    <form method="POST" action="">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="mb-3">
            <label for="adressemail" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="adressemail" name="adressemail" required>
        </div>
        <div class="mb-3">
            <label for="motdepasse" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>
    <p class="text-center mt-3">Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a>.</p>
</div>
</body>
</html>
