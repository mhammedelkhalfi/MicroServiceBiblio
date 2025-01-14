<?php
// Connexion à la base de données
require_once 'connexion.php';

// Récupération des livres à vendre
$sql_livres_a_vendre = "SELECT l.*, lv.prix 
                        FROM livre l
                        INNER JOIN livre_de_vente lv ON l.idLivre = lv.idLivre
                        WHERE l.type = 'vendre' AND l.disponibilite = 1";
$stmt = $pdo->prepare($sql_livres_a_vendre);
$stmt->execute();
$livres_a_vendre = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de l'achat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acheter'])) {
    $livre_id = $_POST['livre_id'];
    $user_id = 1; // ID de l'utilisateur connecté (à remplacer par une session si applicable)
    $prix_achat = $_POST['prix'];

    try {
        // Début de la transaction
        $pdo->beginTransaction();

        // Mettre à jour la disponibilité du livre
        $sql_update_disponibilite = "UPDATE livre SET disponibilite = 0 WHERE idLivre = :livre_id";
        $stmt_update = $pdo->prepare($sql_update_disponibilite);
        $stmt_update->execute([':livre_id' => $livre_id]);

        // Créer une facture
        $sql_insert_facture = "INSERT INTO facture (idUtilisateur, idLivre, montant_total, montant_payer, 
                              montant_rest, datePayment, dateLimite) 
                              VALUES (:user_id, :livre_id, :montant_total, 0, :montant_total, 
                              CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY))";
        $stmt_insert = $pdo->prepare($sql_insert_facture);
        $stmt_insert->execute([
            ':user_id' => $user_id,
            ':livre_id' => $livre_id,
            ':montant_total' => $prix_achat
        ]);

        // Valider la transaction
        $pdo->commit();
        $message = "Livre acheté avec succès pour " . $prix_achat . " €.";
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        $message = "Erreur lors de l'achat : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Acheter des livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Livres à Vendre</h1>
    <a href="livres_disponibles.php?type=vendre" class="btn btn-secondary mb-3">Retour</a>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo strpos($message, 'succès') !== false ? 'success' : 'danger'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped mt-4">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Prix (€)</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($livres_a_vendre as $livre): ?>
            <tr>
                <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                <td><?php echo htmlspecialchars($livre['prix']); ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="livre_id" value="<?php echo $livre['idLivre']; ?>">
                        <input type="hidden" name="prix" value="<?php echo $livre['prix']; ?>">
                        <button type="submit" name="acheter" class="btn btn-success">Acheter</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>