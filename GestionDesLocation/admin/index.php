<?php
require_once '../connexion.php';

// Gestion de la recherche
$search = $_GET['search'] ?? '';

// Récupération des livres pour location uniquement
$query = "
    SELECT idLivre, titre, auteur, image, disponibilite 
    FROM livre 
    WHERE type = 'location'
";
if (!empty($search)) {
    $query .= " AND (titre LIKE :search OR auteur LIKE :search)";
}
$stmt = $pdo->prepare($query);
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->execute();
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres de Location</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestion des Livres de Location</h1>

    <!-- Barre de recherche -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un livre..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
    </form>

    <!-- Bouton pour ajouter un livre -->
    <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#addModal">Ajouter un Livre</button>

    <!-- Tableau des livres -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Image</th>
            <th>Disponibilité</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($livres as $livre): ?>
            <tr>
                <td><?= htmlspecialchars($livre['titre']) ?></td>
                <td><?= htmlspecialchars($livre['auteur']) ?></td>
                <td><img src="<?= htmlspecialchars($livre['image']) ?>" alt="Image du livre" style="width: 50px; height: 50px;"></td>
                <td><?= $livre['disponibilite'] ? 'Disponible' : 'Indisponible' ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="<?= $livre['idLivre'] ?>"
                            data-titre="<?= htmlspecialchars($livre['titre']) ?>"
                            data-auteur="<?= htmlspecialchars($livre['auteur']) ?>"
                            data-image="<?= htmlspecialchars($livre['image']) ?>"
                            data-disponibilite="<?= $livre['disponibilite'] ?>">
                        Modifier
                    </button>
                    <a href="delete.php?id=<?= $livre['idLivre'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal d'ajout -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="add.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Ajouter un Livre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="auteur" class="form-label">Auteur</label>
                        <input type="text" class="form-control" id="auteur" name="auteur" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="disponibilite" class="form-label">Disponibilité</label>
                        <select class="form-select" id="disponibilite" name="disponibilite" required>
                            <option value="1">Disponible</option>
                            <option value="0">Indisponible</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Ajouter</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="edit.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier un Livre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId" name="idLivre">
                    <div class="mb-3">
                        <label for="editTitre" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="editTitre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="editAuteur" class="form-label">Auteur</label>
                        <input type="text" class="form-control" id="editAuteur" name="auteur" required>
                    </div>
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="editDisponibilite" class="form-label">Disponibilité</label>
                        <select class="form-select" id="editDisponibilite" name="disponibilite" required>
                            <option value="1">Disponible</option>
                            <option value="0">Indisponible</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Modifier</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Préremplir le modal de modification
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;

        document.getElementById('editId').value = button.getAttribute('data-id');
        document.getElementById('editTitre').value = button.getAttribute('data-titre');
        document.getElementById('editAuteur').value = button.getAttribute('data-auteur');
        document.getElementById('editDisponibilite').value = button.getAttribute('data-disponibilite');
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
