<?php
session_start();
require 'connexion.php'; // Connexion à la base de données

// Vérification du rôle d'utilisateur
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: login.php"); // Redirige si l'utilisateur n'est pas un administrateur
    exit;
}

// Initialisation des variables
$error = $success = "";

// Fonction pour ajouter un livre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Ajouter un livre
    if ($action === 'add') {
        $titre = $_POST['titre'] ?? '';
        $auteur = $_POST['auteur'] ?? '';
        $disponibilite = $_POST['disponibilite'] ?? 0;
        $type = 'pret'; // Valeur par défaut de type
        $image = $_FILES['image'] ?? null;

        if (!empty($titre) && !empty($auteur)) {
            
            // Gérer le téléchargement de l'image
if ($image && $image['error'] === 0) {
    // Définir le dossier de destination
    $target_dir = "C:/xampp/htdocs/MicroServiceBiblio/images/";
    $target_file = $target_dir . basename($image['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifiez le type d'image
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($image['tmp_name'], $target_file)) {
            // Enregistrer le chemin relatif dans la base de données
            $relative_path = "../images/" . basename($image['name']);
            try {
                $sql = "INSERT INTO livre (titre, auteur, image, disponibilite, type) 
                        VALUES (:titre, :auteur, :image, :disponibilite, :type)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':titre' => $titre,
                    ':auteur' => $auteur,
                    ':image' => $relative_path,
                    ':disponibilite' => $disponibilite,
                    ':type' => $type,
                ]);
                $success = "Livre ajouté avec succès!";
            } catch (PDOException $e) {
                $error = "Erreur lors de l'ajout du livre : " . $e->getMessage();
            }
        } else {
            $error = "Erreur lors du téléchargement de l'image.";
        }
    } else {
        $error = "Seuls les formats jpg, jpeg, png, et gif sont autorisés.";
    }
} else {
    $error = "Veuillez sélectionner une image.";
}

        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }

    // Modifier un livre
    if ($action === 'update') {
        $idLivre = $_POST['idLivre'] ?? '';
        $titre = $_POST['titre'] ?? '';
        $auteur = $_POST['auteur'] ?? '';
        $disponibilite = $_POST['disponibilite'] ?? 0;

        if (!empty($idLivre) && !empty($titre) && !empty($auteur)) {
            try {
                $sql = "UPDATE livre SET titre = :titre, auteur = :auteur, disponibilite = :disponibilite WHERE idLivre = :idLivre";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([ 
                    ':titre' => $titre,
                    ':auteur' => $auteur,
                    ':disponibilite' => $disponibilite,
                    ':idLivre' => $idLivre
                ]);
                $success = "Livre modifié avec succès!";
            } catch (PDOException $e) {
                $error = "Erreur lors de la modification du livre : " . $e->getMessage();
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }

    // Supprimer un livre
    if ($action === 'delete') {
        $idLivre = $_POST['idLivre'] ?? '';
        
        if (!empty($idLivre)) {
            try {
                $sql = "DELETE FROM livre WHERE idLivre = :idLivre";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([ ':idLivre' => $idLivre ]);
                $success = "Livre supprimé avec succès!";
            } catch (PDOException $e) {
                $error = "Erreur lors de la suppression du livre : " . $e->getMessage();
            }
        } else {
            $error = "Erreur : Identifiant du livre manquant.";
        }
    }
}

// Récupérer les livres de la base de données
try {
    $sql = "SELECT * FROM livre where type ='pret'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des livres : " . $e->getMessage();
}

// Récupérer les emprunts
try {
    $sql = "SELECT * FROM livre where type='pret'"; // Assurez-vous que cette table existe dans votre base de données
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des emprunts : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des livres - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="gestion-livres.php">Gestion des livres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion-emprunts.php">Gestion des emprunts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Gestion des profils</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="deconnexion.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Gestion des livres</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addModal">Ajouter un livre</button>

    <!-- Modal pour l'ajout -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Ajouter un livre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre :</label>
                            <input type="text" name="titre" id="titre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="auteur" class="form-label">Auteur :</label>
                            <input type="text" name="auteur" id="auteur" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type :</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="pret" selected>Prêt</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image :</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="disponibilite" class="form-label">Disponibilité :</label>
                            <select name="disponibilite" id="disponibilite" class="form-select" required>
                                <option value="1" selected>Disponible</option>
                                <option value="0">Non disponible</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter le livre</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Titre</th>
                <th scope="col">Auteur</th>
                <th scope="col">Image</th>
                <th scope="col">Disponibilité</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <td><?php echo htmlspecialchars($livre['idLivre']); ?></td>
                    <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                    <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($livre['image']); ?>" width="100"></td>
                    <td><?php echo $livre['disponibilite'] == 1 ? 'Disponible' : 'Non disponible'; ?></td>
                    <td>
                        <!-- Modal de modification -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $livre['idLivre']; ?>">Modifier</button>
                        <!-- Modal de suppression -->
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="idLivre" value="<?php echo $livre['idLivre']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre?');">Supprimer</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal de mise à jour -->
                <div class="modal fade" id="updateModal<?php echo $livre['idLivre']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateModalLabel">Modifier le livre</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="idLivre" value="<?php echo $livre['idLivre']; ?>">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre :</label>
                                        <input type="text" name="titre" id="titre" class="form-control" value="<?php echo htmlspecialchars($livre['titre']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="auteur" class="form-label">Auteur :</label>
                                        <input type="text" name="auteur" id="auteur" class="form-control" value="<?php echo htmlspecialchars($livre['auteur']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="disponibilite" class="form-label">Disponibilité :</label>
                                        <select name="disponibilite" id="disponibilite" class="form-select" required>
                                            <option value="1" <?php echo $livre['disponibilite'] == 1 ? 'selected' : ''; ?>>Disponible</option>
                                            <option value="0" <?php echo $livre['disponibilite'] == 0 ? 'selected' : ''; ?>>Non disponible</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
