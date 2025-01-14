<?php
include 'session_start.php';
include 'connexion.php';
include 'navbar.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch purchase history
    $purchase_stmt = $pdo->prepare("
        SELECT l.titre AS livre_titre, f.montant_total, f.montant_rest, f.datePayment 
        FROM facture f 
        INNER JOIN livre l ON f.idLivre = l.idLivre 
        WHERE f.idUtilisateur = :id AND l.type = 'vendre'
    ");
    $purchase_stmt->execute([':id' => $user_id]);
    $purchases = $purchase_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch rental history
    $rental_stmt = $pdo->prepare("
        SELECT l.titre AS livre_titre, ll.date_emprunt, ll.date_retour 
        FROM livre_de_location ll 
        INNER JOIN livre l ON ll.idLivre = l.idLivre 
        WHERE l.idUtilisateur = :id
    ");
    $rental_stmt->execute([':id' => $user_id]);
    $rentals = $rental_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch borrowing history
    $borrowing_stmt = $pdo->prepare("
        SELECT l.titre AS livre_titre, lp.date_emprunt, lp.date_retour 
        FROM livre_pret lp 
        INNER JOIN livre l ON lp.idLivre = l.idLivre 
        WHERE l.idUtilisateur = :id
    ");
    $borrowing_stmt->execute([':id' => $user_id]);
    $borrowings = $borrowing_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: -webkit-linear-gradient(bottom, #2dbd6e, #a6f77b);
            background-repeat: no-repeat;
            font-family: "Raleway", sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
        }

        .card {
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        h2, h4 {
            font-family: "Raleway SemiBold", sans-serif;
            letter-spacing: 1px;
            text-align: center;
            color: #2c3e50;
        }

        .table {
            background: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 15px;
        }

        th {
            background: -webkit-linear-gradient(right, #a6f77b, #2dbd6e);
            color: white;
            text-align: center;
        }

        td {
            text-align: center;
            padding: 10px;
        }

        p {
            font-family: "Raleway", sans-serif;
            color: #555;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Historique</h2>

    <div class="card">
        <h4>Achats</h4>
        <?php if (!empty($purchases)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Montant Total</th>
                        <th>Montant Restant</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $purchase): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($purchase['livre_titre']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['montant_total']); ?> DH</td>
                            <td><?php echo htmlspecialchars($purchase['montant_rest']); ?> DH</td>
                            <td><?php echo htmlspecialchars($purchase['datePayment']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun achat trouvé.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h4>Locations</h4>
        <?php if (!empty($rentals)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Date Emprunt</th>
                        <th>Date Retour</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentals as $rental): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rental['livre_titre']); ?></td>
                            <td><?php echo htmlspecialchars($rental['date_emprunt']); ?></td>
                            <td><?php echo htmlspecialchars($rental['date_retour']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune location trouvée.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h4>Emprunts</h4>
        <?php if (!empty($borrowings)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Date Emprunt</th>
                        <th>Date Retour</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowings as $borrow): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($borrow['livre_titre']); ?></td>
                            <td><?php echo htmlspecialchars($borrow['date_emprunt']); ?></td>
                            <td><?php echo htmlspecialchars($borrow['date_retour']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun emprunt trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
