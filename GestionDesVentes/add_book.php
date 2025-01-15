<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <h2 class="text-center">Ajouter un Livre</h2>
    <form method="POST" action="process_add_book.php" enctype="multipart/form-data">
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
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-control" id="type" name="type" required>
            
                <option value="vendre">Vendre</option>
               
            </select>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="disponibilite" name="disponibilite">
            <label for="disponibilite" class="form-check-label">Disponible</label>
        </div>
        <div class="mb-3">
    <label for="prix" class="form-label">Prix</label>
    <input type="number" class="form-control" id="prix" name="prix" required>
</div>

        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
    </form>
</div>
</body>
</html>
