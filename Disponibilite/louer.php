<?php
// Connexion à la base de données
require_once 'connexion.php';

// Récupération des livres à louer
$sql_livres_a_louer = "SELECT l.*, ll.prix, ll.duree, ll.date_emprunt, ll.date_retour 
                       FROM livre l
                       INNER JOIN livre_de_location ll ON l.idLivre = ll.idLivre
                       WHERE l.type = 'location' AND l.disponibilite = 1";
$stmt = $pdo->prepare($sql_livres_a_louer);
$stmt->execute();
$livres_a_louer = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la location
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['louer'])) {
    $livre_id = $_POST['livre_id'];
    $user_id = 1; // ID de l'utilisateur connecté (à remplacer par une session si applicable)
    $prix_location = $_POST['prix'];
    $date_emprunt = $_POST['date_emprunt'];
    $date_retour = $_POST['date_retour'];

    try {
        // Début de la transaction
        $pdo->beginTransaction();

        // Mettre à jour la disponibilité du livre
        $sql_update_disponibilite = "UPDATE livre SET disponibilite = 0 WHERE idLivre = :livre_id";
        $stmt_update = $pdo->prepare($sql_update_disponibilite);
        $stmt_update->execute([':livre_id' => $livre_id]);

        // Mettre à jour les dates dans livre_de_location
        $sql_update_dates = "UPDATE livre_de_location 
                            SET date_emprunt = :date_emprunt, date_retour = :date_retour 
                            WHERE idLivre = :livre_id";
        $stmt_dates = $pdo->prepare($sql_update_dates);
        $stmt_dates->execute([
            ':date_emprunt' => $date_emprunt,
            ':date_retour' => $date_retour,
            ':livre_id' => $livre_id
        ]);

        // Créer une facture
        $sql_insert_facture = "INSERT INTO facture (idUtilisateur, idLivre, montant_total, montant_payer, 
                              montant_rest, datePayment, dateLimite) 
                              VALUES (:user_id, :livre_id, :montant_total, 0, :montant_total, 
                              :date_emprunt, :date_retour)";
        $stmt_insert = $pdo->prepare($sql_insert_facture);
        $stmt_insert->execute([
            ':user_id' => $user_id,
            ':livre_id' => $livre_id,
            ':montant_total' => $prix_location,
            ':date_emprunt' => $date_emprunt,
            ':date_retour' => $date_retour
        ]);

        // Valider la transaction
        $pdo->commit();
        $message = "Livre loué avec succès pour " . $prix_location . " €.";
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        $message = "Erreur lors de la location : " . $e->getMessage();
    }
}

// Obtenir la date d'aujourd'hui au format YYYY-MM-DD
$today = date('Y-m-d');
// Obtenir la date dans 30 jours
$default_return_date = date('Y-m-d', strtotime('+30 days'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Louer des livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Livres à Louer</h1>
    <a href="livres_disponibles.php?type=location" class="btn btn-secondary mb-3">Retour</a>

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
            <th>Durée</th>
            <th>Date d'emprunt</th>
            <th>Date de retour</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($livres_a_louer as $livre): ?>
            <tr>
                <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                <td><?php echo htmlspecialchars($livre['prix']); ?></td>
                <td><?php echo htmlspecialchars($livre['duree']); ?></td>
                <td>
                    <form method="POST" action="" class="d-flex flex-column gap-2">
                        <input type="hidden" name="livre_id" value="<?php echo $livre['idLivre']; ?>">
                        <input type="hidden" name="prix" value="<?php echo $livre['prix']; ?>">
                        <input type="date" name="date_emprunt" 
                               value="<?php echo $today; ?>" 
                               min="<?php echo $today; ?>" 
                               required 
                               class="form-control">
                </td>
                <td>
                        <input type="date" name="date_retour" 
                               value="<?php echo $default_return_date; ?>" 
                               min="<?php echo $today; ?>" 
                               required 
                               class="form-control">
                </td>
                <td>
                        <button type="submit" name="louer" class="btn btn-primary">Louer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Ajouter une validation pour s'assurer que la date de retour est après la date d'emprunt
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const dateEmprunt = this.querySelector('[name="date_emprunt"]').value;
        const dateRetour = this.querySelector('[name="date_retour"]').value;
        
        if (dateRetour <= dateEmprunt) {
            e.preventDefault();
            alert('La date de retour doit être postérieure à la date d\'emprunt');
        }
    });
});
</script>

</body>
</html>