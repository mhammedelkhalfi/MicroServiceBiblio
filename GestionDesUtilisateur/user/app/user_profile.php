<?php
include 'session_start.php';
include 'connexion.php';
include 'navbar.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Utilisateur non trouvé.");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: -webkit-linear-gradient(bottom, #2dbd6e, #a6f77b);
            background-repeat: no-repeat;
            font-family: "Raleway", sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
        }

        .card {
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            font-family: "Raleway Thin", sans-serif;
            letter-spacing: 2px;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .profile-detail {
            font-size: 1.1rem;
            font-family: "Raleway", sans-serif;
            color: #2c3e50;
        }

        .profile-detail strong {
            color: #2dbd6e;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="profile-header">Votre Profil</h2>
    <div class="card p-4 mt-3">
        <div class="profile-detail">
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
            <p><strong>Prénom:</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['adressemail']); ?></p>
            <p><strong>Rôle:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($user['status']); ?></p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
