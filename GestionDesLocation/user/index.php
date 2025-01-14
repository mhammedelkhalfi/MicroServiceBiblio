<?php 
session_start();
include '../connexion.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

// Initialize search term
$searchTerm = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
}

// Récupérer les livres de location disponibles
$stmt = $pdo->prepare("
    SELECT l.idLivre, l.titre, l.auteur, ld.prix, l.image 
    FROM livre l 
    JOIN livre_de_location ld ON l.idLivre = ld.idLivre 
    WHERE l.disponibilite = 1 AND l.type = 'location' 
    AND (l.titre LIKE ? OR l.auteur LIKE ?)
");
$stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['valider'])) {
    $idLivre = $_POST['idLivre'];
    $duree = $_POST['duree'];

    // Récupérer le prix du livre
    $stmt = $pdo->prepare("SELECT ld.prix FROM livre_de_location ld WHERE ld.idLivre = ?");
    $stmt->execute([$idLivre]);
    $livre = $stmt->fetch();

    if ($livre) {
        $prixLocation = $livre['prix'];
        $montantTotal = $prixLocation * $duree;

        // Enregistrer la facture dans la base de données
        $stmt = $pdo->prepare("INSERT INTO facture (idUtilisateur, idLivre, montant_total) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['id'], $idLivre, $montantTotal]);

        echo "<div class='alert alert-success'>La location a été validée avec succès ! Montant total: $montantTotal €</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la récupération du prix du livre.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres de Location</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .book-container {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 20px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        .book-image {
            width: 150px; /* Set width for the image box */
            height: auto;
        }
        .book-info {
            padding: 15px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Livres de Location Disponibles</h2>

    <!-- Search Form -->
    <form method="post" action="" class="mb-4">
        <div class="input-group">
            <input type="text" name="searchTerm" value="<?php echo htmlspecialchars($searchTerm); ?>" class="form-control" placeholder="Rechercher par titre ou auteur">
            <button type="submit" name="search" class="btn btn-primary">Rechercher</button>
        </div>
    </form>

    <?php if (count($livres) > 0): ?>
        <?php foreach ($livres as $livre): ?>
        <div class="book-container">
            <img src="<?php echo htmlspecialchars($livre['image']); ?>" alt="<?php echo htmlspecialchars($livre['titre']); ?>" class="book-image">
            <div class="book-info">
                <h5><?php echo htmlspecialchars($livre['titre']); ?></h5>
                <p><strong>Auteur:</strong> <?php echo htmlspecialchars($livre['auteur']); ?></p>
                <p><strong>Prix par jour:</strong> <?php echo htmlspecialchars($livre['prix']); ?> €</p>
                <form method="post" action="">
                    <input type="hidden" name="idLivre" value="<?php echo htmlspecialchars($livre['idLivre']); ?>">
                    <div class="input-group mb-3">
                        <input type="number" name="duree" required min="1" class="form-control" placeholder="Durée (jours)">
                        <button type="submit" name="valider" class="btn btn-success">Valider</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning">Aucun livre trouvé avec ce critère de recherche.</div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
