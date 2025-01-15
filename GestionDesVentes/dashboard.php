<?php
// Connexion à la base de données
$host = '127.0.0.1';
$dbname = 'microserviceebook';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Démarrer la session
    session_start();
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        throw new Exception("Utilisateur non connecté.");
    }

    // Requête pour récupérer le statut de l'utilisateur
    $sqlUser = "SELECT status FROM utilisateur WHERE id = :userId";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtUser->execute();
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Utilisateur non trouvé.");
    }

    $isAuthorized = ($user['status'] === 'AUTHORIZED');

    // Requête pour récupérer les livres à vendre directement depuis la table livre
    $sql = "SELECT idLivre, titre, auteur, image, disponibilite 
            FROM livre 
            WHERE type = 'vendre'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres à Vendre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <button class="btn btn-danger float-end" id="logoutBtn">Se Déconnecter</button>
        <h1 class="text-center mb-4">Livres à Vendre</h1>
        <div class="row">
            <?php foreach ($livres as $livre): ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img src="<?= htmlspecialchars($livre['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($livre['titre']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                            <p class="card-text">Auteur : <?= htmlspecialchars($livre['auteur']) ?></p>
                            <?php if ($livre['disponibilite'] == 1): ?>
                                <?php if ($isAuthorized): ?>
                                    <a href="../GestionDesPayement/payer.php?idLivre=<?= $livre['idLivre'] ?>" class="btn btn-custom">Acheter</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Non autorisé</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Indisponible</button>
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
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'deconnexion.php';
                }
            });
        });
    </script>
</body>
</html>
