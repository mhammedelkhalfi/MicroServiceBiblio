<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "microserviceebook");

if ($mysqli->connect_error) {
    die("Échec de la connexion: " . $mysqli->connect_error);
}

// Récupérer les données des livres par type
$result = $mysqli->query("SELECT type, COUNT(*) AS count FROM livre WHERE type IN ('location', 'vendre', 'pret') GROUP BY type");
if (!$result) {
    die("Erreur dans la requête SQL: " . $mysqli->error);
}
$livreData = ['location' => 0, 'vendre' => 0, 'pret' => 0];
while ($row = $result->fetch_assoc()) {
    $livreData[$row['type']] = $row['count'];
}

// Récupérer les données des utilisateurs par rôle
$result = $mysqli->query("SELECT role, COUNT(*) AS count FROM utilisateur WHERE role IN ('ADMIN', 'UTILISATEUR') GROUP BY role");
if (!$result) {
    die("Erreur dans la requête SQL: " . $mysqli->error);
}
$utilisateurData = ['ADMIN' => 0, 'UTILISATEUR' => 0];
while ($row = $result->fetch_assoc()) {
    $utilisateurData[$row['role']] = $row['count'];
}

// Récupérer les données des utilisateurs par statut
$result = $mysqli->query("SELECT status, COUNT(*) AS count FROM utilisateur WHERE status IN ('AUTHORIZED', 'BLACKLISTED', 'LACKOFRESOURCES') GROUP BY status");
if (!$result) {
    die("Erreur dans la requête SQL: " . $mysqli->error);
}
$statusData = ['AUTHORIZED' => 0, 'BLACKLISTED' => 0, 'LACKOFRESOURCES' => 0];
while ($row = $result->fetch_assoc()) {
    $statusData[$row['status']] = $row['count'];
}


// Récupérer les données des livres par disponibilité
$result = $mysqli->query("SELECT disponibilite, COUNT(*) AS count FROM livre GROUP BY disponibilite");
if (!$result) {
    die("Erreur dans la requête SQL: " . $mysqli->error);
}
$disponibiliteData = ['DISPONIBLE' => 0, 'NON DISPONIBLE' => 0];
while ($row = $result->fetch_assoc()) {
    if ($row['disponibilite'] == 1) {
        $disponibiliteData['DISPONIBLE'] = $row['count'];
    } else {
        $disponibiliteData['NON DISPONIBLE'] = $row['count'];
    }
}


// Récupérer les statistiques des utilisateurs selon le domaine de leur email
$result = $mysqli->query("
    SELECT 
        CASE
            WHEN adressemail LIKE '%@etud.iga.ac.ma' THEN 'IGA'
            WHEN adressemail LIKE '%@etud.emsi.ac.ma' THEN 'EMSI'
            ELSE 'Autres'
        END AS domaine,
        COUNT(*) AS count
    FROM utilisateur
    GROUP BY domaine
");
if (!$result) {
    die("Erreur dans la requête SQL: " . $mysqli->error);
}
$emailData = ['IGA' => 0, 'EMSI' => 0, 'Autres' => 0];
while ($row = $result->fetch_assoc()) {
    $emailData[$row['domaine']] = $row['count'];
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        h1 {
            color: #343a40;
            text-align: center;
            margin-bottom: 30px;
        }
        .navbar {
            margin-bottom: 30px;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">E-Library Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_book.php">Ajouter un Livre</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_books.php">Gérer les Livres</a></li>
                    <li class="nav-item"><a class="nav-link" href="../GestionDesUtilisateur/admin/app/admin_dashboard.php">Gérer les Utilisateurs</a></li>
                </ul>
                <span class="navbar-text ms-auto">Connecté en tant que <strong><?php echo $_SESSION['user_name']; ?></strong></span>
                <a href="deconnexion.php" class="btn btn-outline-light ms-3">Se déconnecter</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Statistiques des Livres, Utilisateurs et Statuts</h1>
        <div class="row">
            <div class="col-md-4">
                <canvas id="livreChart"></canvas>
            </div>
            <div class="col-md-4">
                <canvas id="utilisateurChart"></canvas>
            </div>
            <div class="col-md-4">
                <canvas id="statusChart"></canvas>
            </div>
          
            <div class="col-md-4">
                <canvas id="disponibiliteChart"></canvas>
            </div>
            <div class="col-md-4">
                <canvas id="emailChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const livreData = {
            labels: ['Location', 'Vente', 'Prêt'],
            datasets: [{
                data: [<?php echo $livreData['location']; ?>, <?php echo $livreData['vendre']; ?>, <?php echo $livreData['pret']; ?>],
                backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe']
            }]
        };
        const utilisateurData = {
            labels: ['Admin', 'Utilisateur'],
            datasets: [{
                data: [<?php echo $utilisateurData['ADMIN']; ?>, <?php echo $utilisateurData['UTILISATEUR']; ?>],
                backgroundColor: ['#ffcc00', '#ff6600']
            }]
        };
        const statusData = {
            labels: ['Authorized', 'Blacklisted', 'Lack of Resources'],
            datasets: [{
                data: [<?php echo $statusData['AUTHORIZED']; ?>, <?php echo $statusData['BLACKLISTED']; ?>, <?php echo $statusData['LACKOFRESOURCES']; ?>],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107']
            }]
        };
     
        const disponibiliteData = {
            labels: ['Disponible', 'Non Disponible'],
            datasets: [{
                data: [<?php echo $disponibiliteData['DISPONIBLE']; ?>, <?php echo $disponibiliteData['NON DISPONIBLE']; ?>],
                backgroundColor: ['#4caf50', '#f44336']
            }]
        };

        const emailData = {
            labels: ['IGA', 'EMSI', 'Autres'],
            datasets: [{
                data: [<?php echo $emailData['IGA']; ?>, <?php echo $emailData['EMSI']; ?>, <?php echo $emailData['Autres']; ?>],
                backgroundColor: ['#ffcd56', '#4bc0c0', '#9966ff']
            }]
        };

        new Chart(document.getElementById('livreChart'), { type: 'pie', data: livreData });
        new Chart(document.getElementById('utilisateurChart'), { type: 'pie', data: utilisateurData });
        new Chart(document.getElementById('statusChart'), { type: 'pie', data: statusData });
        new Chart(document.getElementById('disponibiliteChart'), { type: 'pie', data: disponibiliteData });
        new Chart(document.getElementById('emailChart'), { type: 'pie', data: emailData });
        </script>
       
    </script>
</body>
</html>
