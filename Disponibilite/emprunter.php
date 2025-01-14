<?php
include 'connexion.php';

// Récupération du livre à emprunter si on vient de livres_disponibles.php
if (isset($_POST['livre_id'])) {
    $livre_id = $_POST['livre_id'];
    
    // Récupérer les informations du livre
    $sql = "SELECT l.*, lp.duree FROM livre l 
            LEFT JOIN livre_pret lp ON l.idLivre = lp.idLivre 
            WHERE l.idLivre = :id AND l.type = 'pret'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $livre_id]);
    $livre = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Traitement de l'emprunt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmer_emprunt'])) {
    $livre_id = $_POST['livre_id'];
    $date_emprunt = $_POST['date_emprunt'];
    $date_retour = $_POST['date_retour'];
    $user_id = 1; // À remplacer par l'ID de l'utilisateur connecté

    try {
        // Début de la transaction
        $pdo->beginTransaction();

        // Mettre à jour la disponibilité du livre
        $sql = "UPDATE livre SET disponibilite = 0 WHERE idLivre = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $livre_id]);

        // Mettre à jour les dates dans livre_pret
        $sql = "UPDATE livre_pret 
                SET date_emprunt = :date_emprunt, 
                    date_retour = :date_retour 
                WHERE idLivre = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $livre_id,
            'date_emprunt' => $date_emprunt,
            'date_retour' => $date_retour
        ]);

        // Valider la transaction
        $pdo->commit();

        // Redirection avec un message de succès
        header("Location: livres_disponibles.php?type=pret&success=1");
        exit;
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        $error = "Erreur lors de l'emprunt : " . $e->getMessage();
    }
}

// Obtenir la date d'aujourd'hui au format YYYY-MM-DD
$today = date('Y-m-d');
// Obtenir la date dans 14 jours (durée par défaut pour un emprunt)
$default_return_date = date('Y-m-d', strtotime('+14 days'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emprunter un livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Emprunter un livre</h1>
    <a href="livres_disponibles.php?type=pret" class="btn btn-secondary mb-3">Retour</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($livre)): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($livre['titre']); ?></h5>
                <p class="card-text">
                    <strong>Auteur :</strong> <?php echo htmlspecialchars($livre['auteur']); ?><br>
                    <strong>Durée maximale :</strong> <?php echo htmlspecialchars($livre['duree']); ?>
                </p>

                <form method="POST" action="" class="mt-3">
                    <input type="hidden" name="livre_id" value="<?php echo $livre['idLivre']; ?>">
                    
                    <div class="mb-3">
                        <label for="date_emprunt" class="form-label">Date d'emprunt</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_emprunt" 
                               name="date_emprunt" 
                               value="<?php echo $today; ?>" 
                               min="<?php echo $today; ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="date_retour" class="form-label">Date de retour</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_retour" 
                               name="date_retour" 
                               value="<?php echo $default_return_date; ?>" 
                               min="<?php echo $today; ?>" 
                               required>
                    </div>

                    <button type="submit" name="confirmer_emprunt" class="btn btn-primary">Confirmer l'emprunt</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            Livre non trouvé ou non disponible pour l'emprunt.
        </div>
    <?php endif; ?>
</div>

<script>
// Validation pour s'assurer que la date de retour est après la date d'emprunt
document.querySelector('form').addEventListener('submit', function(e) {
    const dateEmprunt = document.querySelector('#date_emprunt').value;
    const dateRetour = document.querySelector('#date_retour').value;
    
    if (dateRetour <= dateEmprunt) {
        e.preventDefault();
        alert('La date de retour doit être postérieure à la date d\'emprunt');
    }
});
</script>

</body>
</html>