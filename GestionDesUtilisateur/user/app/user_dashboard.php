<?php
// user_dashboard.php
include 'session_start.php';
include 'connexion.php';
include 'navbar.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the user ID from the query string (for admin) or the session (for regular users)
$user_id = $_GET['id'] ?? $_SESSION['user_id'];

// Access control: admins can view any user, regular users can only view their own data
if (isset($_GET['id']) && $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: user_dashboard.php");
    exit;
}

try {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    
    //facture
    $rental_query = $pdo->prepare("
    SELECT 
        l.titre AS livre_titre,
        f.montant_total,
        f.montant_rest,
        ll.date_emprunt,
        ll.date_retour
    FROM livre l
    INNER JOIN livre_de_location ll ON l.idLivre = ll.idLivre
    LEFT JOIN facture f ON f.idLivre = l.idLivre AND f.idUtilisateur = :id
    WHERE l.type = 'location'
");
$rental_query->execute([':id' => $user_id]);
$rentals = $rental_query->fetchAll(PDO::FETCH_ASSOC);

    // Initialize credit and notifications
    $credit = 500; // Initial credit
    $notifications = [];

    // Purchase history and credit calculation
    $purchase_history = $pdo->prepare("SELECT f.*, l.titre AS livre_titre, l.type AS livre_type FROM facture f 
        INNER JOIN livre l ON f.idLivre = l.idLivre 
        WHERE f.idUtilisateur = :id AND l.type = 'vendre'");
    $purchase_history->execute([':id' => $user_id]);
    $purchases = $purchase_history->fetchAll(PDO::FETCH_ASSOC);

    $credit_exceeded_books = [];
    foreach ($purchases as $purchase) {
        $credit -= $purchase['montant_rest'];
        if ($credit < 0) {
            $credit_exceeded_books[] = $purchase['livre_titre'] . '(' . $purchase['livre_type'] . ')';
        }
    }
    

    // Rental history and credit calculation
    $rental_history = $pdo->prepare("SELECT f.montant_total, f.montant_rest, l.titre AS livre_titre, l.type AS livre_type FROM facture f 
        INNER JOIN livre l ON f.idLivre = l.idLivre 
        WHERE f.idUtilisateur = :id AND l.type = 'location'");
    $rental_history->execute([':id' => $user_id]);
    $rentals = $rental_history->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rentals as $rental) {
        $credit -= $rental['montant_rest'];
        if ($credit < 0) {
            $credit_exceeded_books[] = $rental['livre_titre'] . '(' . $rental['livre_type'] . ')';
        }
    }

    // Update status based on credit and notifications
    if ($credit < 0) {
        $books_list = implode(', ', $credit_exceeded_books);
        $notifications[] = [
            'message' => "Votre crédit est épuisé ($credit DH) de $books_list. Votre statut a été changé en 'LACKOFRESOURCES'.",
            'type' => 'danger'
        ];
        $status_update = $pdo->prepare("UPDATE utilisateur SET status = 'LACKOFRESOURCES' WHERE id = :id");
        $status_update->execute([':id' => $user_id]);
        $user['status'] = 'LACKOFRESOURCES';
    } elseif ($credit > 0 && $user['status'] === 'LACKOFRESOURCES') {
        $status_update = $pdo->prepare("UPDATE utilisateur SET status = 'AUTHORIZED' WHERE id = :id");
        $status_update->execute([':id' => $user_id]);
        $user['status'] = 'AUTHORIZED';
    }

    // Borrowing notifications
    $borrowing_notifications_stmt = $pdo->prepare("SELECT l.titre AS livre_titre, lp.date_emprunt, lp.date_retour 
        FROM livre_pret lp 
        INNER JOIN livre l ON lp.idLivre = l.idLivre 
        WHERE l.idUtilisateur = :id");
    $borrowing_notifications_stmt->execute([':id' => $user_id]);
    $borrowings = $borrowing_notifications_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($borrowings as &$borrow) {
        $date_emprunt = new DateTime($borrow['date_emprunt']);
        $date_retour = new DateTime($borrow['date_retour']);

        if ($date_emprunt > $date_retour) {
            $status_update = $pdo->prepare("UPDATE utilisateur SET status = 'BLACKLISTED' WHERE id = :id");
            $status_update->execute([':id' => $user_id]);
            $user['status'] = 'BLACKLISTED';
            $notifications[] = [
                'message' => "Le livre '" . htmlspecialchars($borrow['livre_titre']) . "' (emprunté) a une période invalide. Votre statut a été changé en 'BLACKLISTED'.",
                'type' => 'danger'
            ];
        } elseif ($date_emprunt == $date_retour) {
            $notifications[] = [
                'message' => "Le livre '" . htmlspecialchars($borrow['livre_titre']) . "' (emprunté) doit être retourné aujourd'hui.",
                'type' => 'warning'
            ];
        } else {
            $date_diff = $date_retour->diff($date_emprunt)->days;
            $borrow['duree'] = $date_diff . ' jours';
            if ($date_diff === 1) {
                $notifications[] = [
                    'message' => "Le livre '" . htmlspecialchars($borrow['livre_titre']) . "' (emprunté) doit être retourné demain.",
                    'type' => 'info'
                ];
            }
        }
    }

    // Rental notifications
$rental_notifications_stmt = $pdo->prepare("
SELECT 
    l.titre AS livre_titre, 
    ll.date_emprunt, 
    ll.date_retour 
FROM livre_de_location ll 
INNER JOIN livre l ON ll.idLivre = l.idLivre 
WHERE l.idUtilisateur = :id
");
$rental_notifications_stmt->execute([':id' => $user_id]);
$rentals = $rental_notifications_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rentals as &$rental) {
$date_emprunt = new DateTime($rental['date_emprunt']);
$date_retour = new DateTime($rental['date_retour']);

if ($date_emprunt > $date_retour) {
    // Invalid period
    $status_update = $pdo->prepare("UPDATE utilisateur SET status = 'BLACKLISTED' WHERE id = :id");
    $status_update->execute([':id' => $user_id]);
    $user['status'] = 'BLACKLISTED';
    $notifications[] = [
        'message' => "Le livre '" . htmlspecialchars($rental['livre_titre']) . "' (loué) a une période invalide. Votre statut a été changé en 'BLACKLISTED'.",
        'type' => 'danger'
    ];
} elseif ($date_emprunt == $date_retour) {
    // Return due today
    $notifications[] = [
        'message' => "Le livre '" . htmlspecialchars($rental['livre_titre']) . "' (loué) doit être retourné aujourd'hui.",
        'type' => 'warning'
    ];
} else {
    // Valid period - calculate duration
    $date_diff = $date_retour->diff($date_emprunt)->days;
    $rental['jours_duree'] = $date_diff > 0 ? $date_diff . ' jours' : 'N/A';
    
    if ($date_diff === 1) {
        // Return due tomorrow
        $notifications[] = [
            'message' => "Le livre '" . htmlspecialchars($rental['livre_titre']) . "' (loué) doit être retourné demain.",
            'type' => 'info'
        ];
    }
}
}

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: -webkit-linear-gradient(bottom, #2dbd6e, #a6f77b);
            background-repeat: no-repeat;
            font-family: "Raleway", sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            margin: auto;
            max-width: 1200px;
            min-width: 1200px;
        }

        .card {
            border-radius: 15px;
            background: #fbfbfb;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        h3,
        h2 {
            font-family: "Raleway Thin", sans-serif;
            letter-spacing: 2px;
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .btn-danger {
            background: -webkit-linear-gradient(right, #a6f77b, #2dbd6e);
            border: none;
            border-radius: 21px;
            font-weight: bold;
            color: white;
        }

        .table {
            background: linear-gradient(to bottom, #fdfcfb, #e2d1c3);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: -webkit-linear-gradient(right, #a6f77b, #2dbd6e);
            color: white;
        }

        .table thead {
            background: -webkit-linear-gradient(right, #a6f77b, #2dbd6e);
            color: white;
        }

        .list-group-item {
            border: none;
            font-size: 0.95rem;
        }

        .list-group-item-warning {
            background: #fff3cd;
            color: #856404;
        }

        .list-group-item-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="section">
        <h2>Vos informations :</h2>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
                <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Email :</strong> <?php echo htmlspecialchars($user['adressemail']); ?></p>
                <p><strong>Rôle :</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                <p><strong>Crédit :</strong> <?php echo htmlspecialchars($credit); ?> DH / <small>Max: 500 DH</small></p>
                <p><strong>Status :</strong> <?php echo htmlspecialchars($user['status']); ?></p>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Notifications :</h2>
        <ul class="list-group">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item list-group-item-<?php echo htmlspecialchars($notification['type']); ?> rounded-pill">
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-muted">Aucune notification pour le moment.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div>
        <h2>Historique :</h2>
    </div>

    <div class="section">
        <h4>Achats :</h4>
        <?php if (!empty($purchases)): ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Livre</th>
                    <th>Montant</th>
                    <th>Montant restant</th>
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
            <p class="text-muted">Aucun achat enregistré.</p>
        <?php endif; ?>
    </div>

    
    <div class="section">
    <h4>Locations :</h4>
    <?php if (!empty($rentals)): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Date Emprunt</th>
                    <th>Date Retour</th>
                    <th>Durée (jours)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rentals as $rental): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rental['livre_titre']); ?></td>
                        <td><?php echo htmlspecialchars($rental['date_emprunt']); ?></td>
                        <td><?php echo htmlspecialchars($rental['date_retour']); ?></td>
                        <td><?php echo htmlspecialchars($rental['jours_duree'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">Aucune location enregistrée.</p>
    <?php endif; ?>
</div>




<div class="section">
    <h4>Emprunts :</h4>
    <?php if (!empty($borrowings)): ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Livre</th>
                <th>Date d'emprunt</th>
                <th>Date de retour</th>
                <th>Durée</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($borrowings as $borrow): ?>
                <tr>
                    <td><?php echo htmlspecialchars($borrow['livre_titre'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($borrow['date_emprunt'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($borrow['date_retour'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($borrow['duree'] ?? 'N/A'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">Aucun emprunt enregistré.</p>
    <?php endif; ?>
</div>

    <div class="text-center">
        <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
