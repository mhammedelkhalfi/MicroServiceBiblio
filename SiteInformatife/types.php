<?php
include 'connexion.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque en Ligne - Choix du Type de Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            color: white;
        }

        .book-choice-container {
            backdrop-filter: blur(5px);
            background-color: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .choice-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .choice-card:hover {
            transform: translateY(-5px);
        }

        .btn {
            padding: 1rem 2rem;
            font-size: 1.2rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, #4e54c8, #8f94fb);
        }

        .btn-warning {
            background: linear-gradient(45deg, #f6b93b, #fad961);
        }

        .btn-success {
            background: linear-gradient(45deg, #43c6ac, #191654);
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="book-choice-container">
        <h1 class="text-center mb-5">Bibliothèque en Ligne</h1>
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="choice-card text-center">
                    <div class="icon">
                        <i class="fas fa-book-reader text-primary"></i>
                    </div>
                    <h3 class="mb-4">Emprunter</h3>
                    <a href="../GestionDesEmprunt/login.php" class="btn btn-primary w-100">
                        Livres à Emprunter
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="choice-card text-center">
                    <div class="icon">
                        <i class="fas fa-money-bill-wave text-warning"></i>
                    </div>
                    <h3 class="mb-4">Louer</h3>
                    <a href="comming.php" class="btn btn-warning w-100">
                        Livres à Louer
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="choice-card text-center">
                    <div class="icon">
                        <i class="fas fa-shopping-cart text-success"></i>
                    </div>
                    <h3 class="mb-4">Acheter</h3>
                    <a href="../GestionDesVentes/login.php" class="btn btn-success w-100">
                        Livres à Vendre
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>