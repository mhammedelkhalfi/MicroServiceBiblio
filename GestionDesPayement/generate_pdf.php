<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

    // Récupération des données du livre
    $sql = "SELECT l.idLivre, l.titre, v.prix
            FROM livre l
            JOIN livre_de_vente v ON l.idLivre = v.idLivre
            WHERE l.idLivre = :idLivre";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idLivre', $idLivre, PDO::PARAM_INT);
    $stmt->execute();
    $livre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$livre) {
        die("Livre non trouvé.");
    }

    // Configuration de Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);

    // Contenu HTML du PDF
    $html = "
    <h1 style='text-align: center;'>Votre Facture de Paiement</h1>
    <p><strong>Utilisateur ID :</strong> {$userId}</p>
    <p><strong>Nom Livre :</strong> {$livre['titre']}</p>
    <p><strong>Prix de Livre :</strong> {$livre['prix']} DH</p>
    <p><strong>Date d'achat  :</strong> " . date('d-m-Y') . "</p>
    <p style='text-align: center; margin-top: 50px;'>Merci pour votre achat !</p>
    ";

    // Charger le contenu HTML
    $dompdf->loadHtml($html);

    // (Optionnel) Définir la taille et l'orientation de la page
    $dompdf->setPaper('A4', 'portrait');

    // Générer le PDF
    $dompdf->render();

    // Envoyer le PDF au navigateur pour téléchargement
    $dompdf->stream("facture_livre_{$idLivre}.pdf", ["Attachment" => true]);

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
?>
