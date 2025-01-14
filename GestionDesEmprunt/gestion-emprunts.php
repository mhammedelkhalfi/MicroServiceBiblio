<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

// Vérification du rôle d'utilisateur
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: login.php"); // Redirige si l'utilisateur n'est pas un administrateur
    exit;
}

// Requête pour récupérer les informations des utilisateurs
$sqlUtilisateurs = "
SELECT 
    utilisateur_id AS ID,
    utilisateur_nom AS Nom,
    utilisateur_email AS Email,
    utilisateur_role AS Role,
    utilisateur_status AS Status,
    utilisateur_credit AS Credit,
    utilisateur_created_at AS DateCreation
FROM 
    vue_utilisateur_livre_pret
GROUP BY 
    utilisateur_id
";

$sqlEmprunts = "
SELECT 
    utilisateur_id,
    livre_titre,
    livre_id AS LivreID,
    pret_duree,
    pret_date_emprunt,
    pret_date_retour
FROM 
    vue_utilisateur_livre_pret
";

try {
    $stmtUtilisateurs = $pdo->query($sqlUtilisateurs);
    $utilisateurs = $stmtUtilisateurs->fetchAll(PDO::FETCH_ASSOC);

    $stmtEmprunts = $pdo->query($sqlEmprunts);
    $emprunts = $stmtEmprunts->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de l'exécution de la requête : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .close {
            color: #aaa;
            font-size: 30px;
            cursor: pointer;
            position: absolute;
            top: 5px;
            right: 10px;
        }
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
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="gestion-livres.php">Gestion des livres empruntés</a></li>
                <li class="nav-item"><a class="nav-link" href="gestion-emprunts.php">Gestion des emprunts</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Gestion des profils</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="text-center">Gestion des Utilisateurs</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Status</th>
                <th>Crédit</th>
                <th>Date de Création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($utilisateurs)): ?>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?= htmlspecialchars($utilisateur['ID']); ?></td>
                        <td><?= htmlspecialchars($utilisateur['Nom']); ?></td>
                        <td><?= htmlspecialchars($utilisateur['Email']); ?></td>
                        <td><?= htmlspecialchars($utilisateur['Role']); ?></td>
                        <td><?= htmlspecialchars($utilisateur['Status']); ?></td>
                        <td><?= htmlspecialchars($utilisateur['Credit']); ?></td>
                        <td><?= htmlspecialchars($utilisateur['DateCreation']); ?></td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="openModal(<?= htmlspecialchars($utilisateur['ID']); ?>)">Consulter</button>
                            <button class="btn btn-warning btn-sm" onclick="openUpdateModal(<?= htmlspecialchars($utilisateur['ID']); ?>)">Mettre à jour</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Aucun utilisateur trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Détails des emprunts</h2>
        <div id="modal-body"></div>
    </div>
</div>

<!-- Modal Update -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUpdateModal()">&times;</span>
        <h2>Modifier un Emprunt</h2>
        <form id="updateForm" method="POST" action="update_emprunt.php">
            <input type="hidden" name="utilisateur_id" id="update_user_id">
            <input type="hidden" name="livre_id" id="update_livre_id">
            <div class="mb-3">
                <label for="pret_date_retour" class="form-label">Date de retour:</label>
                <input type="date" class="form-control" name="pret_date_retour" id="pret_date_retour" onchange="calculateDuration()">
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>

<script>
    const emprunts = <?= json_encode($emprunts); ?>;

    // Ouvrir le modal pour consulter les emprunts
    function openModal(userId) {
        const modal = document.getElementById('modal');
        const userEmprunts = emprunts.filter(e => e.utilisateur_id == userId);
        let modalContent = '';

        userEmprunts.forEach(emprunt => {
            modalContent += `<p><strong>Titre:</strong> ${emprunt.livre_titre}</p>`;
            modalContent += `<p><strong>Date d'emprunt:</strong> ${emprunt.pret_date_emprunt}</p>`;
            modalContent += `<p><strong>Date de retour:</strong> ${emprunt.pret_date_retour}</p>`;
        });

        document.getElementById('modal-body').innerHTML = modalContent;
        modal.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }

    // Ouvrir le modal pour mettre à jour les emprunts
    function openUpdateModal(userId) {
        const updateModal = document.getElementById('updateModal');
        const userEmprunts = emprunts.filter(e => e.utilisateur_id == userId);

        if (userEmprunts.length > 0) {
            const emprunt = userEmprunts[0];

            document.getElementById('update_user_id').value = emprunt.utilisateur_id;
            document.getElementById('update_livre_id').value = emprunt.LivreID;
            document.getElementById('pret_date_retour').value = emprunt.pret_date_retour || '';

            updateModal.style.display = 'block';
        }
    }

    function closeUpdateModal() {
        document.getElementById('updateModal').style.display = 'none';
    }

    // Fonction pour calculer la durée en jours
    function calculateDuration() {
        const dateEmprunt = new Date(document.getElementById('pret_duree').value);
        const dateRetour = new Date(document.getElementById('pret_date_retour').value);

        if (dateEmprunt && dateRetour) {
            const timeDiff = dateRetour - dateEmprunt;
            const duration = Math.ceil(timeDiff / (1000 * 3600 * 24));
            document.getElementById('pret_duree').value = duration;
        }
    }
</script>

</body>
</html>
