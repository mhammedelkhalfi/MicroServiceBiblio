<?php
session_start(); // Démarre une session si elle n'est pas déjà démarrée
require 'connexion.php'; // Connexion à la base de données

// Initialisation des variables pour éviter les avertissements
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'login') {
        // Gestion de la connexion
        $adressemail = $_POST['adressemail'] ?? '';
        $password = $_POST['motdepasse'] ?? '';

        if (!empty($adressemail) && !empty($password)) {
            try {
                $sql = "SELECT * FROM utilisateur WHERE adressemail = :adressemail";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':adressemail' => $adressemail]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['motdepasse'])) {
                    // Connexion réussie, démarrage de la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_role'] = $user['role'];

                    // Redirection selon le rôle
                    if ($user['role'] === 'ADMIN') {
                        header("Location: adminDashbord.php");
                    } else {
                        header("Location: userDashboard.php");
                    }
                    exit;
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            } catch (PDOException $e) {
                $error = "Erreur lors de la connexion : " . $e->getMessage();
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    } elseif ($action === 'register') {
        // Gestion de l'enregistrement
        $adressemail = $_POST['adressemail'] ?? '';
        $password = $_POST['motdepasse'] ?? '';
        $nom = $_POST['nom'] ?? '';
        $role = $_POST['role'] ?? '';

        if (!empty($adressemail) && !empty($password) && !empty($nom) && !empty($role)) {
            if (!in_array($role, ['ADMIN', 'USER'])) {
                $error = "Rôle invalide.";
            } else {
                try {
                    // Vérification si l'email existe déjà
                    $sql = "SELECT * FROM utilisateur WHERE adressemail = :adressemail";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':adressemail' => $adressemail]);
                    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingUser) {
                        $error = "L'email est déjà utilisé.";
                    } else {
                        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                        $sql = "INSERT INTO utilisateur (adressemail, motdepasse, nom, role) VALUES (:adressemail, :password, :nom, :role)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':adressemail' => $adressemail,
                            ':password' => $hashedPassword,
                            ':nom' => $nom,
                            ':role' => $role
                        ]);

                        $success = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                    }
                } catch (PDOException $e) {
                    $error = "Erreur lors de l'enregistrement : " . $e->getMessage();
                }
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion / Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Connexion / Inscription</h3>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <ul class="nav nav-tabs" id="authTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Se connecter</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">S'inscrire</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="authTabContent">
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form method="post" action="">
                                <input type="hidden" name="action" value="login">
                                <div class="mb-3">
                                    <label for="adressemail" class="form-label">Email :</label>
                                    <input type="email" name="adressemail" id="adressemail" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe :</label>
                                    <input type="password" name="motdepasse" id="password" class="form-control" required>
                                </div>
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary">Se connecter</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="register" role="tabpanel">
                            <form method="post" action="">
                                <input type="hidden" name="action" value="register">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom :</label>
                                    <input type="text" name="nom" id="nom" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="adressemail" class="form-label">Email :</label>
                                    <input type="email" name="adressemail" id="adressemail" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe :</label>
                                    <input type="password" name="motdepasse" id="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Rôle :</label>
                                    <select name="role" id="role" class="form-select" required>
                                        <option value="USER">Utilisateur</option>
                                        <option value="ADMIN">Administrateur</option>
                                    </select>
                                </div>
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
