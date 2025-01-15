<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "microserviceebook");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérification de l'ID du livre
if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    // Requête pour récupérer les informations du livre
    $sql = "SELECT * FROM livre WHERE idLivre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        echo "Livre non trouvé.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les nouvelles valeurs du formulaire
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;

    // Gestion de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        // Vérification de l'extension de l'image
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $imageExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        
        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
            // Déplacer l'image dans le dossier "uploads"
            $imagePath = 'uploads/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        } else {
            echo "Extension de fichier non autorisée.";
            exit;
        }
    } else {
        // Si aucune image n'est téléchargée, garder l'ancienne image
        $imagePath = $book['image'];
    }

    // Requête pour mettre à jour le livre
    $sql = "UPDATE livre SET titre = ?, auteur = ?, image = ?, disponibilite = ? WHERE idLivre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $titre, $auteur, $imagePath, $disponibilite, $bookId);

    if ($stmt->execute()) {
        header("Location: manage_books.php?message=update_success");
    } else {
        echo "Erreur lors de la mise à jour : " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Modifier le Livre</h2>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">E-Library Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="admin_dashboard.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_book.php">Ajouter un Livre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_books.php">Gérer les Livres</a>
                </li>
            </ul>
          
            <a href="deconnexion.php" class="btn btn-outline-light">Se déconnecter</a>
        </div>
    </div>
</nav>

        <form action="edit_book.php?id=<?php echo $bookId; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $book['titre']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="auteur" class="form-label">Auteur</label>
                <input type="text" class="form-control" id="auteur" name="auteur" value="<?php echo $book['auteur']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image du Livre</label>
                <input type="file" class="form-control" id="image" name="image">
                <small class="form-text text-muted">Sélectionnez une nouvelle image ou laissez vide pour garder l'ancienne.</small>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="disponibilite" name="disponibilite" <?php echo $book['disponibilite'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="disponibilite">Disponible</label>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>

        <!-- Affichage de l'image -->
        <div class="mt-4">
            <h5>Image actuelle du Livre</h5>
            <img src="<?php echo $book['image']; ?>" alt="Image du livre" class="img-fluid" style="max-width: 200px;">
        </div>
    </div>

    <script>
        // SweetAlert2 pour les notifications
        <?php if (isset($_GET['message']) && $_GET['message'] == 'update_success') { ?>
            Swal.fire({
                icon: 'success',
                title: 'Livre mis à jour!',
                text: 'Le livre a été mis à jour avec succès.',
                showConfirmButton: false,
                timer: 1500
            });
        <?php } ?>
    </script>
</body>
</html>
