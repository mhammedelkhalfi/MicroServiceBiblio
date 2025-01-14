<?php
include 'connexion.php';

$type = $_GET['type'] ?? 'pret'; // Type par défaut : pret (au lieu de emprunt)

// Requête pour récupérer les livres disponibles
if ($type === 'location') {
    $sql = "
        SELECT l.idLivre, l.titre, l.auteur, ld.prix
        FROM livre l
        INNER JOIN livre_de_location ld ON l.idLivre = ld.idLivre
        WHERE l.type = :type AND l.disponibilite = 1
    ";
} elseif ($type === 'vendre') { // 'vendre' au lieu de 'vente' selon votre enum
    $sql = "
        SELECT l.idLivre, l.titre, l.auteur, lv.prix
        FROM livre l
        INNER JOIN livre_de_vente lv ON l.idLivre = lv.idLivre
        WHERE l.type = :type AND l.disponibilite = 1
    ";
} else {
    // Pour le type 'pret'
    $sql = "SELECT idLivre, titre, auteur FROM livre WHERE type = :type AND disponibilite = 1";
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['type' => $type]);
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">
        Livres Disponibles - <?php echo ucfirst($type); ?>
    </h1>
    <a href="types.php" class="btn btn-secondary mb-3">Retour</a>

    <?php if (count($livres) > 0): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <?php if ($type === 'location' || $type === 'vendre'): ?>
                    <th>Prix</th>
                <?php endif; ?>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                    <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                    <?php if ($type === 'location' || $type === 'vendre'): ?>
                        <td><?php echo htmlspecialchars($livre['prix']); ?> €</td>
                    <?php endif; ?>
                    <td>
                        <?php if ($type === 'pret'): ?>
                            <form method="POST" action="emprunter.php">
                                <input type="hidden" name="livre_id" value="<?php echo $livre['idLivre']; ?>">
                                <button type="submit" class="btn btn-primary">Emprunter</button>
                            </form>
                        <?php elseif ($type === 'location'): ?>
                            <form method="POST" action="louer.php">
                                <input type="hidden" name="livre_id" value="<?php echo $livre['idLivre']; ?>">
                                <button type="submit" class="btn btn-warning">Louer</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="acheter.php">
                                <input type="hidden" name="livre_id" value="<?php echo $livre['idLivre']; ?>">
                                <button type="submit" class="btn btn-success">Acheter</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun livre disponible dans cette catégorie.</p>
    <?php endif; ?>
</div>
</body>
</html>