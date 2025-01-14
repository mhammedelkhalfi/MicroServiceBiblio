<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

// Vérification des paramètres
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Paramètres invalides.");
}

$user_id = $_GET['id'];

// Requête pour récupérer l'historique des emprunts de l'utilisateur
$sql_historique = "SELECT l.idLivre, l.titre, lp.date_emprunt, lp.date_retour 
                   FROM livre_pret lp
                   JOIN livre l ON lp.idLivre = l.idLivre
                   WHERE lp.idUtilisateur = :user_id";
$stmt_historique = $pdo->prepare($sql_historique);
$stmt_historique->execute(['user_id' => $user_id]);
$emprunts = $stmt_historique->fetchAll(PDO::FETCH_ASSOC);

// Retrieve success message from session if it exists
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Emprunts</title>
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
        .disabled-btn {
            pointer-events: none;
            opacity: 0.5;
        }
        .strikethrough {
            text-decoration: line-through;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
    </div>
</nav>

<div class="container mt-5">
    <h2>Historique des emprunts</h2>

    <?php if ($success_message): ?>
        <script>
            alert("<?php echo htmlspecialchars($success_message); ?>");
        </script>
        <?php unset($_SESSION['success_message']); // Clear the success message ?>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Date d'emprunt</th>
                <th>Date de retour</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $emprunt): ?>
                <tr class="<?php echo ($emprunt['date_retour'] !== NULL) ? 'strikethrough' : ''; ?>">
                    <td><?php echo htmlspecialchars($emprunt['titre']); ?></td>
                    <td><?php echo htmlspecialchars($emprunt['date_emprunt']); ?></td>
                    <td><?php echo $emprunt['date_retour'] ? htmlspecialchars($emprunt['date_retour']) : 'En cours'; ?></td>
                    <td>
                        <?php if ($emprunt['date_retour'] === NULL): ?>
                            <a href="retour.php?id=<?php echo $emprunt['idLivre']; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-danger">Retourner</a>
                        <?php else: ?>
                            <button class="btn btn-danger disabled-btn" disabled>Retour effectué</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
