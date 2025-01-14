<?php
// Connexion à la base de données
$host = '127.0.0.1';
$dbname = 'microserviceebook';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'ID de l'utilisateur connecté (par exemple, depuis la session)
    session_start();
    $userId = $_SESSION['user_id']; // Assurez-vous que l'ID de l'utilisateur est stocké dans la session

    // Requête pour récupérer l'utilisateur et son statut
    $sqlUser = "SELECT status FROM utilisateur WHERE id = :userId";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtUser->execute();
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur est autorisé
    $isAuthorized = ($user['status'] === 'AUTHORIZED');

    // Requête pour récupérer les livres à vendre
    $sql = "SELECT l.idLivre, l.titre, l.auteur, l.image, l.disponibilite, v.prix
            FROM livre l
            JOIN livre_de_vente v ON l.idLivre = v.idLivre
            WHERE l.type = 'vendre'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres à Vendre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card-img-top {
            object-fit: cover;
            height: 250px;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
        .card-body {
            text-align: center;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1rem;
            color: #6c757d;
        }
        .logout-btn {
            float: right;
            margin: 10px;
            background-color: #dc3545;
            color: white;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Bouton Se Déconnecter -->
        <button class="btn logout-btn" id="logoutBtn">Se Déconnecter</button>

        <h1 class="mb-4 text-center">Livres à Vendre</h1>
        <div class="row">
            <?php foreach ($livres as $livre): ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img src="<?= htmlspecialchars($livre['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($livre['titre']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                            <p class="card-text">Auteur: <?= htmlspecialchars($livre['auteur']) ?></p>
                            <p class="card-text">Prix: <?= htmlspecialchars($livre['prix']) ?> DH</p>
                            <?php if ($livre['disponibilite'] == 1): ?>
                                <?php if ($isAuthorized): ?>
                                    <a href="../GestionDesPayement/payer.php?idLivre=<?= $livre['idLivre'] ?>" class="btn btn-custom">Acheter</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Vous n'êtes pas autorisé à acheter ce livre</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Indisponible</button>
                                <div class="message">
                                    <p><i class="fas fa-exclamation-triangle"></i> Livre n'est pas disponible.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.getElementById('logoutBtn').addEventListener('click', function () {
            Swal.fire({
                title: 'Êtes-vous sûr de vouloir vous déconnecter ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, déconnectez-moi',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'deconnexion.php';
                }
            });
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
