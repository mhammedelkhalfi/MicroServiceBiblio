<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
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
<div class="container mt-5">
    <h2 class="text-center mb-4">Gérer les Livres à Vendre</h2>

    
    <table id="booksTable" class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Image</th>
            <th>Disponibilité</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Connexion à la base de données
        $conn = new mysqli("localhost", "root", "", "microserviceebook");

        // Vérification de la connexion
        if ($conn->connect_error) {
            die("Connexion échouée : " . $conn->connect_error);
        }

        // Requête pour récupérer les livres avec type = 'vendre'
        $sql = "SELECT * FROM livre WHERE type = 'vendre'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $index = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$index}</td>
                        <td>{$row['titre']}</td>
                        <td>{$row['auteur']}</td>
                        <td><img src='{$row['image']}' alt='Image du livre' style='width: 100px; height: auto;'></td>
                        <td>" . ($row['disponibilite'] ? "Disponible" : "Indisponible") . "</td>
                        <td>
                            <button class='btn btn-primary btn-sm' onclick='window.location.href=\"edit_book.php?id={$row['idLivre']}\"'>Modifier</button>
                            <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['idLivre']}'>Supprimer</button>
                        </td>
                      </tr>";
                $index++;
            }
            
        } else {
            echo "<tr><td colspan='6' class='text-center'>Aucun livre trouvé</td></tr>";
        }

        $conn->close();
        ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        // Initialisation de DataTables
        $('#booksTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            }
        });

        // Gestion de la suppression
        $('.delete-btn').on('click', function () {
            const bookId = $(this).data('id');
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirection vers un script PHP pour la suppression
                    window.location.href = `delete_book.php?id=${bookId}`;
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
<?php
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'success') {
        echo "<div class='alert alert-success'>Le livre a été supprimé avec succès.</div>";
    } elseif ($_GET['message'] == 'update_success') {
        echo "<div class='alert alert-success'>Le livre a été mis à jour avec succès.</div>";
    }
}
?>