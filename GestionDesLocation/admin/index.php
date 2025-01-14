<?php
require_once '../connexion.php';

// Gestion de la recherche
$search = $_GET['search'] ?? '';

// Récupération des livres de location
$query = "
    SELECT l.idLivre, l.titre, l.auteur, ll.prix, ll.duree, ll.date_emprunt, ll.date_retour
    FROM livre l
    INNER JOIN livre_de_location ll ON l.idLivre = ll.idLivre
    WHERE l.type = 'location' 
";
if (!empty($search)) {
    $query .= " AND (l.titre LIKE :search OR l.auteur LIKE :search)";
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
            <th>Prix</th>
            <th>Durée (jours)</th>
            <th>Date d'emprunt</th>
            <th>Date de retour</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($livres as $livre): ?>
            <tr>
                <td><?= htmlspecialchars($livre['titre']) ?></td>
                <td><?= htmlspecialchars($livre['auteur']) ?></td>
                <td><?= htmlspecialchars($livre['prix']) ?> €/jour</td>
                <td><?= htmlspecialchars($livre['duree'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($livre['date_emprunt'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($livre['date_retour'] ?? 'N/A') ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                            data-id="<?= $livre['idLivre'] ?>"
                            data-titre="<?= htmlspecialchars($livre['titre']) ?>"
                            data-auteur="<?= htmlspecialchars($livre['auteur']) ?>"
                            data-prix="<?= $livre['prix'] ?>"
                            data-date-emprunt="<?= $livre['date_emprunt'] ?>"
                            data-date-retour="<?= $livre['date_retour'] ?>">
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
            <form method="POST" action="add.php">
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
                        <label for="prix" class="form-label">Prix par jour</label>
                        <input type="number" class="form-control" id="prix" name="prix" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="choix" class="form-label">Choisir une méthode</label><br>
                        <input type="radio" id="choixDuree" name="choix" value="duree" checked>
                        <label for="choixDuree">Durée en jours</label>
                        <input type="radio" id="choixDates" name="choix" value="dates">
                        <label for="choixDates">Dates fixes</label>
                    </div>
                    <div id="dureeContainer" class="mb-3">
                        <label for="duree" class="form-label">Durée (jours)</label>
                        <input type="number" class="form-control" id="duree" name="duree">
                    </div>
                    <div id="datesContainer" class="mb-3" style="display: none;">
                        <label for="date_emprunt" class="form-label">Date d'emprunt</label>
                        <input type="date" class="form-control" id="date_emprunt" name="date_emprunt">
                        <label for="date_retour" class="form-label">Date de retour</label>
                        <input type="date" class="form-control" id="date_retour" name="date_retour">
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
            <form method="POST" action="edit.php">
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
                        <label for="editPrix" class="form-label">Prix par jour</label>
                        <input type="number" class="form-control" id="editPrix" name="prix" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDateEmprunt" class="form-label">Date d'emprunt</label>
                        <input type="date" class="form-control" id="editDateEmprunt" name="date_emprunt" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDateRetour" class="form-label">Date de retour</label>
                        <input type="date" class="form-control" id="editDateRetour" name="date_retour" required>
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
    // Gestion des champs de formulaire (ajout)
    const choixDuree = document.getElementById('choixDuree');
    const choixDates = document.getElementById('choixDates');
    const dureeContainer = document.getElementById('dureeContainer');
    const datesContainer = document.getElementById('datesContainer');

    choixDuree.addEventListener('change', () => {
        if (choixDuree.checked) {
            dureeContainer.style.display = 'block';
            datesContainer.style.display = 'none';
        }
    });

    choixDates.addEventListener('change', () => {
        if (choixDates.checked) {
            dureeContainer.style.display = 'none';
            datesContainer.style.display = 'block';
        }
    });

    // Préremplir le modal de modification
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;

        document.getElementById('editId').value = button.getAttribute('data-id');
        document.getElementById('editTitre').value = button.getAttribute('data-titre');
        document.getElementById('editAuteur').value = button.getAttribute('data-auteur');
        document.getElementById('editPrix').value = button.getAttribute('data-prix');
        document.getElementById('editDateEmprunt').value = button.getAttribute('data-date-emprunt');
        document.getElementById('editDateRetour').value = button.getAttribute('data-date-retour');
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
