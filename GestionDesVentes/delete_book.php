<?php
// Vérifier si un ID de livre est passé dans l'URL
if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    // Connexion à la base de données
    $conn = new mysqli("localhost", "root", "", "microserviceebook");

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }

    // Requête pour supprimer le livre
    $sql = "DELETE FROM livre WHERE idLivre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookId);

    if ($stmt->execute()) {
        // Redirection après la suppression
        echo "<script>
               
                window.location.href = 'delete_book.php';
              </script>";
    } else {
        echo "<script>
              
                window.location.href = 'manage_books.php';
              </script>";
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();
} else {
    echo "<script>
         
            window.location.href = 'manage_books.php';
          </script>";
}
?>
