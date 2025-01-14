<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="gestion-livres.php">Gestion des livres a Emprunté</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion-emprunts.php">Gestion des emprunts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../GestionDesUtilisateur/admin/app/admin_dashboard.php">Gestion des profils</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="deconnexion.php">Déconnexion</a>
                </li>

            </ul>
        </div>
    </div>
</nav>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
