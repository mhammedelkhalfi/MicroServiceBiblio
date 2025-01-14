<?php
// login.php
include 'session_start.php';
include 'connexion.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM utilisateur WHERE adressemail = :adressemail";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':adressemail' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['motdepasse'])) {
            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'ADMIN') {
                 header("Location: ../../admin/app/admin_dashboard.php"); // Corrected path for admin dashboard
            } else {
                header("Location: user_dashboard.php"); // Redirect for regular users
            }
            exit;
        } else {
            $error = "Adresse email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
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
    <style>
        body {
            background: -webkit-linear-gradient(bottom, #2dbd6e, #a6f77b);
            font-family: "Raleway", sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            border-radius: 15px;
            background: #fbfbfb;
            box-shadow: 1px 2px 8px rgba(0, 0, 0, 0.65);
            padding: 20px;
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
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <h3 class="text-center mb-4">Connexion</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email :</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe :</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
                <p class="text-center mt-3">
                    Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
