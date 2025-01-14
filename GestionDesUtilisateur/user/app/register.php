<?php
// register.php
include 'session_start.php';
include 'connexion.php';

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $status = 'ACTIVE'; // Default status for new users

    try {
        $sql = "INSERT INTO utilisateur (nom, prenom, adressemail, motdepasse, role, status) 
                VALUES (:nom, :prenom, :adressemail, :motdepasse, :role, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':adressemail' => $email,
            ':motdepasse' => $password,
            ':role' => $role,
            ':status' => $status,
        ]);
        // Redirect to the login page after successful registration
        header("Location: login.php?message=success");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate email error
            $message = "L'adresse email est déjà utilisée.";
            $messageType = "danger";
        } else {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "danger";
        }
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
    <style>
        body {
            background: -webkit-linear-gradient(bottom, #2dbd6e, #a6f77b);
            font-family: "Raleway", sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .card {
            border-radius: 15px;
            background: #fbfbfb;
            box-shadow: 1px 2px 8px rgba(0, 0, 0, 0.65);
            padding: 20px;
            width: 100%;
            max-width: 600px;
        }
        .btn-primary {
            background: -webkit-linear-gradient(right, #a6f77b, #2dbd6e);
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: bold;
        }
        .btn-primary:hover {
            background: -webkit-linear-gradient(right, #2dbd6e, #a6f77b);
        }
        h3 {
            font-family: "Raleway SemiBold", sans-serif;
            color: #2c3e50;
            letter-spacing: 1px;
        }
        a {
            color: #2dbd6e;
            font-weight: bold;
        }
        a:hover {
            color: #1a7d4a;
        }
        .alert {
            border-radius: 8px;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h3 class="text-center mb-4">Créer un compte</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom :</label>
                <input type="text" name="nom" id="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom :</label>
                <input type="text" name="prenom" id="prenom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email :</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle :</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="UTILISATEUR">Utilisateur</option>
                    <option value="ADMIN">Administrateur</option>
                </select>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </div>
            <p class="text-center mt-3">
                Vous avez déjà un compte ? <a href="login.php">Connexion</a>
            </p>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
