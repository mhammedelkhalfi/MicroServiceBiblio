<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

// Vérification du rôle d'utilisateur
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: login.php"); // Redirige si l'utilisateur n'est pas un administrateur
    exit;
}

// Vérification de l'ID de l'utilisateur dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Utilisateur invalide");
}

// Récupération de l'ID de l'utilisateur
$user_id = $_GET['id'];

// Requête pour récupérer l'historique des emprunts de l'utilisateur
$sql = "SELECT livre_pret.idLivre, livre_pret.date_emprunt, livre_pret.date_retour, livre.titre
        FROM livre_pret
        JOIN livre ON livre_pret.idLivre = livre.idLivre
        WHERE livre_pret.idLivre IN (SELECT idLivre FROM livre WHERE idUtilisateur = :user_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer les informations de l'utilisateur
$sql_user = "SELECT nom, prenom FROM utilisateur WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur n'existe pas
if (!$user) {
    die("Utilisateur non trouvé");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des emprunts - <?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></title>
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="gestion-livres.php">Gestion des livres à Emprunter</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="GestionDesEmprunts.php">Gestion des emprunts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Gestion des profils</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="deconnexion.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Historique des emprunts de <?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></h2>
    
    <?php if (empty($emprunts)): ?>
        <p>Aucun emprunt trouvé pour cet utilisateur.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Emprunt</th>
                    <th>Titre du Livre</th>
                    <th>Date d'Emprunt</th>
                    <th>Date de Retour</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $emprunt): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emprunt['idLivre']); ?></td>
                        <td><?php echo htmlspecialchars($emprunt['titre']); ?></td>
                        <td><?php echo htmlspecialchars($emprunt['date_emprunt']); ?></td>
                        <td><?php echo htmlspecialchars($emprunt['date_retour']); ?></td>
                        <td>
                        <a href="retour.php?id=<?php echo $emprunt['idLivre']; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-info">Retourner le Livre</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
