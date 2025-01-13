<?php
// Démarrer une session
session_start();
require 'connexion.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirigez vers la page de connexion si non connecté
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, nom, prenom, adressemail, motdepasse, role, status, credit, created_at FROM utilisateur WHERE id = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

// Récupérer les livres disponibles pour emprunt
$stmt = $pdo->prepare("SELECT idLivre, titre, auteur, image, disponibilite, type FROM livre WHERE disponibilite = 1");
$stmt->execute();
$livresDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emprunt de Livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .nav-link:hover {
            color: #ffcc00 !important;
        }
        .text-danger {
            color: red !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Tableau de Bord</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Historiques</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="deconnexion.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Bienvenue, <?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?> !</h1>
    <p><strong>Email :</strong> <?php echo htmlspecialchars($user['adressemail']); ?></p>
    <p><strong>Crédit :</strong> <?php echo htmlspecialchars($user['credit']); ?> crédits</p>

    <h3>Livres disponibles pour emprunt :</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Image</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livresDisponibles as $livre): ?>
                <tr>
                    <td><?php echo htmlspecialchars($livre['idLivre']); ?></td>
                    <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                    <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($livre['image']); ?>" alt="Image du livre" width="50">
                    </td>
                    <td><?php echo htmlspecialchars($livre['type']); ?></td>
                    <td>
                        <form method="post" action="emprunter.php">
                            <input type="hidden" name="idLivre" value="<?php echo $livre['idLivre']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <button type="submit" class="btn btn-primary">Emprunter</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
