<?php
// Connexion à la base de données
$host = '127.0.0.1';
$dbname = 'microserviceebook';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    session_start();
    $userId = $_SESSION['user_id'];
    $idLivre = $_GET['idLivre'];

    $sql = "SELECT l.idLivre, l.titre, v.prix
            FROM livre l
            JOIN livre_de_vente v ON l.idLivre = v.idLivre
            WHERE l.idLivre = :idLivre";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idLivre', $idLivre, PDO::PARAM_INT);
    $stmt->execute();
    $livre = $stmt->fetch(PDO::FETCH_ASSOC);

    $montantTotal = $livre['prix'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $montantPayer = $_POST['montant_payer'];

        $montantTotal = round($montantTotal, 2);
        $montantPayer = round($montantPayer, 2);

        if ($montantPayer <= $montantTotal) {
            $montantRest = $montantTotal - $montantPayer;
            $dateLimite = ($montantRest == 0) ? date('Y-m-d') : date('Y-m-d', strtotime('+7 days'));
            $datePayment = date('Y-m-d');

            $sqlCredit = "SELECT credit FROM utilisateur WHERE id = :userId";
            $stmtCredit = $pdo->prepare($sqlCredit);
            $stmtCredit->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtCredit->execute();
            $user = $stmtCredit->fetch(PDO::FETCH_ASSOC);
            $creditActuel = $user['credit'];

            $nouveauCredit = $creditActuel - $montantRest;

            $sqlInsert = "INSERT INTO facture (idUtilisateur, idLivre, montant_total, montant_payer, montant_rest, dateLimite, datePayment)
                          VALUES (:idUtilisateur, :idLivre, :montantTotal, :montantPayer, :montantRest, :dateLimite, :datePayment)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':idUtilisateur', $userId, PDO::PARAM_INT);
            $stmtInsert->bindParam(':idLivre', $idLivre, PDO::PARAM_INT);
            $stmtInsert->bindParam(':montantTotal', $montantTotal, PDO::PARAM_STR);
            $stmtInsert->bindParam(':montantPayer', $montantPayer, PDO::PARAM_STR);
            $stmtInsert->bindParam(':montantRest', $montantRest, PDO::PARAM_STR);
            $stmtInsert->bindParam(':dateLimite', $dateLimite, PDO::PARAM_STR);
            $stmtInsert->bindParam(':datePayment', $datePayment, PDO::PARAM_STR);
            $stmtInsert->execute();

            $sqlUpdateDisponibilite = "UPDATE livre SET disponibilite = 0 WHERE idLivre = :idLivre";
            $stmtUpdate = $pdo->prepare($sqlUpdateDisponibilite);
            $stmtUpdate->bindParam(':idLivre', $idLivre, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $sqlUpdateCredit = "UPDATE utilisateur SET credit = :nouveauCredit WHERE id = :userId";
            $stmtUpdateCredit = $pdo->prepare($sqlUpdateCredit);
            $stmtUpdateCredit->bindParam(':nouveauCredit', $nouveauCredit, PDO::PARAM_STR);
            $stmtUpdateCredit->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtUpdateCredit->execute();

            $sqlUpdateLivre = "UPDATE livre SET idUtilisateur = :idUtilisateur WHERE idLivre = :idLivre";
            $stmtUpdateLivre = $pdo->prepare($sqlUpdateLivre);
            $stmtUpdateLivre->bindParam(':idUtilisateur', $userId, PDO::PARAM_INT);
            $stmtUpdateLivre->bindParam(':idLivre', $idLivre, PDO::PARAM_INT);
            $stmtUpdateLivre->execute();


            echo json_encode(['status' => 'success', 'idLivre' => $idLivre]);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => "Le montant payé ne doit pas dépasser le montant total."]);
            exit;
        }
    }
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
    <title>Paiement</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.25rem;
            text-align: center;
            padding: 1rem;
            border-radius: 10px 10px 0 0;
        }

        .btn {
            width: 100%;
            margin-top: 1rem;
        }

        .logout-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 10px 15px;
            font-size: 1.25rem;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <button class="btn logout-btn" id="logoutBtn"><i class="fas fa-sign-out-alt"></i></button>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Facture de Paiement</h2>
            </div>
            <div class="card-body">
                <form id="paymentForm" data-idLivre="<?= $livre['idLivre'] ?>">
                    <div class="mb-3">
                        <label for="montant_total" class="form-label">Montant Total</label>
                        <input type="text" class="form-control" id="montant_total" value="<?= htmlspecialchars($montantTotal) ?> DH" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="montant_payer" class="form-label">Montant à Payer</label>
                        <input type="number" class="form-control" id="montant_payer" name="montant_payer" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-success" id="submitBtn" data-idLivre="<?= $livre['idLivre'] ?>">Valider le Paiement</button>
                    <a href="generate_pdf.php?idLivre=<?= $livre['idLivre'] ?>" class="btn btn-primary">Télécharger la Facture</a>
                    <a href="../GestionDesVentes/dashboard.php" class="btn btn-secondary">Retour à la Page d'Achat</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = document.getElementById('submitBtn');
            const idLivre = this.getAttribute('data-idLivre');

            // Le bouton est désactivé pour le livre en question
            submitButton.disabled = true;
            submitButton.textContent = 'Paiement en cours...';

            fetch('', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Succès', 'Facture bien saisie', 'success').then(() => {
                        // Désactiver le bouton uniquement pour ce livre
                        document.querySelectorAll(`button[data-idLivre="${idLivre}"]`).forEach(button => {
                            button.disabled = true;
                            button.textContent = 'Paiement effectué';
                        });
                    });
                } else {
                    Swal.fire('Erreur', data.message, 'error');
                    // Le bouton reste désactivé en cas d'erreur
                    submitButton.textContent = 'Valider le Paiement';
                }
            })
            .catch(error => {
                Swal.fire('Erreur', 'Une erreur est survenue.', 'error');
                // Le bouton reste désactivé en cas d'erreur
                submitButton.textContent = 'Valider le Paiement';
            });
        });

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
