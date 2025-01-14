<?php
session_start();
include 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adressemail = $_POST['adressemail'];
    $motdepasse = $_POST['motdepasse'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE adressemail = ?");
    $stmt->execute([$adressemail]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($motdepasse, $utilisateur['motdepasse'])) {
        $_SESSION['id'] = $utilisateur['id'];
        $_SESSION['role'] = $utilisateur['role'];

        // Redirection selon le rôle
        if ($utilisateur['role'] == 'ADMIN') {
            header("Location: admin/index.php");
        } else {
            header("Location: user/index.php");
        }
        exit();
    } else {
        $error_message = "Identifiants invalides. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Connexion</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="adressemail" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="adressemail" name="adressemail" required>
                        </div>
                        <div class="mb-3">
                            <label for="motdepasse" class="form-label">Mot de Passe</label>
                            <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>
                    <p class="text-center mt-3">Pas encore inscrit ? <a href="register.php">S'inscrire</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
