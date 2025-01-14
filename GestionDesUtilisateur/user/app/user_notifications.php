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
    // Initialize notifications array
    $notifications = [];

    // Initialize credit limit
    $credit = 500; // Initial credit

    // Fetch user details for credit-related notifications
    $user_stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
    $user_stmt->execute([':id' => $user_id]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Utilisateur non trouvé.");
    }

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

    // Credit-related notification
    $lack_of_resources_notification = "";
    if ($credit < 0) {
        $books_list = implode(', ', $credit_exceeded_books);
        $lack_of_resources_notification = "Votre crédit est épuisé ($credit DH) de $books_list. Votre statut a été changé en 'LACKOFRESOURCES'.";

        // Update user status to 'LACKOFRESOURCES'
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

    foreach ($borrowings as $borrow) {
        $date_emprunt = new DateTime($borrow['date_emprunt']);
        $date_retour = new DateTime($borrow['date_retour']);
        if ($date_emprunt > $date_retour) {
            // Blacklist the user for invalid period
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
            $notifications[] = [
                'message' => "Le livre '" . htmlspecialchars($borrow['livre_titre']) . "' (emprunté) a une période valide.",
                'type' => 'info'
            ];
        }
    }

    // Rental notifications
    $rental_notifications_stmt = $pdo->prepare("SELECT l.titre AS livre_titre, ll.date_emprunt, ll.date_retour 
        FROM livre_de_location ll 
        INNER JOIN livre l ON ll.idLivre = l.idLivre 
        WHERE l.idUtilisateur = :id");
    $rental_notifications_stmt->execute([':id' => $user_id]);
    $rentals = $rental_notifications_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rentals as $rental) {
        $date_emprunt = new DateTime($rental['date_emprunt']);
        $date_retour = new DateTime($rental['date_retour']);
        if ($date_emprunt > $date_retour) {
            // Blacklist the user for invalid period
            $status_update = $pdo->prepare("UPDATE utilisateur SET status = 'BLACKLISTED' WHERE id = :id");
            $status_update->execute([':id' => $user_id]);
            $user['status'] = 'BLACKLISTED';
            $notifications[] = [
                'message' => "Le livre '" . htmlspecialchars($rental['livre_titre']) . "' (loué) a une période invalide. Votre statut a été changé en 'BLACKLISTED'.",
                'type' => 'danger'
            ];
        } elseif ($date_emprunt == $date_retour) {
            $notifications[] = [
                'message' => "Le livre '" . htmlspecialchars($rental['livre_titre']) . "' (loué) doit être retourné aujourd'hui.",
                'type' => 'warning'
            ];
        } else {
            $notifications[] = [
                'message' => "Le livre '" . htmlspecialchars($rental['livre_titre']) . "' (loué) a une période valide.",
                'type' => 'info'
            ];
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
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: -webkit-linear-gradient(bottom, #2dbd6e, #a6f77b);
            font-family: "Raleway", sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .card {
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            padding: 20px;
        }

        h2 {
            font-family: "Raleway Thin", sans-serif;
            letter-spacing: 2px;
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .list-group-item-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .list-group-item-warning {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Notifications</h2>
        <ul class="list-group">
            <?php if (!empty($lack_of_resources_notification)): ?>
                <li class="list-group-item list-group-item-danger rounded-pill">
                    <?php echo htmlspecialchars($lack_of_resources_notification); ?>
                </li>
            <?php endif; ?>
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item list-group-item-<?php echo htmlspecialchars($notification['type']); ?> rounded-pill">
                        <?php echo htmlspecialchars($notification['message']); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-muted rounded-pill">Aucune notification pour le moment.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
