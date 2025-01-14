<?php
// dashboardAdmin.php
include '../../user/app/session_start.php';
include '../../user/app/connexion.php';
include '../../user/app/navbar.php'; 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: ../../user/app/login.php");
    exit;
}

$message = "";
$messageType = "";

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create or Update User/Admin
    if (isset($_POST['action']) && ($_POST['action'] === 'create' || $_POST['action'] === 'update')) {
        $id = $_POST['id'] ?? null;
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

        try {
            if ($_POST['action'] === 'create') {
                $sql = "INSERT INTO utilisateur (nom, prenom, adressemail, motdepasse, role, status) 
                        VALUES (:nom, :prenom, :email, :password, :role, :status)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':password' => $password,
                    ':role' => $role,
                    ':status' => $status,
                ]);
                $message = "Utilisateur créé avec succès !";
                $messageType = "success";
            } else {
                $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, adressemail = :email, 
                        role = :role, status = :status" .
                        ($password ? ", motdepasse = :password" : "") . " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $params = [
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':role' => $role,
                    ':status' => $status,
                    ':id' => $id,
                ];
                if ($password) {
                    $params[':password'] = $password;
                }
                $stmt->execute($params);
                $message = "Utilisateur mis à jour avec succès !";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "danger";
        }
    }

    // Delete User/Admin
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $sql = "DELETE FROM utilisateur WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $message = "Utilisateur supprimé avec succès !";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Fetch Users and Admins
$users = $pdo->query("SELECT * FROM utilisateur WHERE role = 'UTILISATEUR'")->fetchAll(PDO::FETCH_ASSOC);
$admins = $pdo->query("SELECT * FROM utilisateur WHERE role = 'ADMIN'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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
            background: #fbfbfb;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .btn-primary {
            background: -webkit-linear-gradient(right, #a6f77b, #2dbd6e);
            border: none;
            border-radius: 25px;
            color: white;
        }
        .btn-danger {
            border-radius: 25px;
        }
        h3 {
            font-family: "Raleway SemiBold", sans-serif;
            color: #2c3e50;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h3 class="text-center">Dashboard Admin</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> text-center">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Users CRUD -->
    <div class="card">
        <div class="card-body">
            <h4>Gestion des Utilisateurs</h4>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">Ajouter Utilisateur</button>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                        <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($user['adressemail']); ?></td>
                        <td><?php echo htmlspecialchars($user['status']); ?></td>
                        <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                data-id="<?php echo htmlspecialchars($user['id']); ?>"
                                data-nom="<?php echo htmlspecialchars($user['nom']); ?>"
                                data-prenom="<?php echo htmlspecialchars($user['prenom']); ?>"
                                data-email="<?php echo htmlspecialchars($user['adressemail']); ?>"
                                data-role="UTILISATEUR"
                                data-status="<?php echo htmlspecialchars($user['status']); ?>">
                            Modifier
                        </button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                        <a href="../../user/app/user_dashboard.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-info btn-sm">Voir Détails</a>
                    </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Admins CRUD -->
    <div class="card">
        <div class="card-body">
            <h4>Gestion des Administrateurs</h4>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">Ajouter Administrateur</button>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['id']); ?></td>
                        <td><?php echo htmlspecialchars($admin['nom']); ?></td>
                        <td><?php echo htmlspecialchars($admin['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($admin['adressemail']); ?></td>
                        <td><?php echo htmlspecialchars($admin['status']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                    data-id="<?php echo htmlspecialchars($admin['id']); ?>"
                                    data-nom="<?php echo htmlspecialchars($admin['nom']); ?>"
                                    data-prenom="<?php echo htmlspecialchars($admin['prenom']); ?>"
                                    data-email="<?php echo htmlspecialchars($admin['adressemail']); ?>"
                                    data-role="ADMIN"
                                    data-status="<?php echo htmlspecialchars($admin['status']); ?>">
                                Modifier
                            </button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($admin['id']); ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Ajouter un utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="create-nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="create-nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="create-prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="create-prenom" name="prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="create-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="create-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="create-role" class="form-label">Rôle</label>
                        <select class="form-select" id="create-role" name="role" required>
                            <option value="UTILISATEUR">Utilisateur</option>
                            <option value="ADMIN">Administrateur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create-status" class="form-label">Statut</label>
                        <select class="form-select" id="create-status" name="status" required>
                            <option value="AUTHORIZED">AUTHORIZED</option>
                            <option value="BLACKLISTED">BLACKLISTED</option>
                            <option value="LACKOFRESOURCES">LACKOFRESOURCES</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create-password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="create-password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="edit-nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="edit-prenom" name="prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-role" class="form-label">Rôle</label>
                        <select class="form-select" id="edit-role" name="role" required>
                            <option value="UTILISATEUR">Utilisateur</option>
                            <option value="ADMIN">Administrateur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Statut</label>
                        <select class="form-select" id="edit-status" name="status" required>
                            <option value="AUTHORIZED">AUTHORIZED</option>
                            <option value="BLACKLISTED">BLACKLISTED</option>
                            <option value="LACKOFRESOURCES">LACKOFRESOURCES</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-password" class="form-label">Mot de passe (laisser vide pour ne pas modifier)</label>
                        <input type="password" class="form-control" id="edit-password" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="action" value="update" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        document.getElementById('edit-id').value = button.getAttribute('data-id');
        document.getElementById('edit-nom').value = button.getAttribute('data-nom');
        document.getElementById('edit-prenom').value = button.getAttribute('data-prenom');
        document.getElementById('edit-email').value = button.getAttribute('data-email');
        document.getElementById('edit-role').value = button.getAttribute('data-role');
        document.getElementById('edit-status').value = button.getAttribute('data-status');
    });
</script>
</body>
</html>
