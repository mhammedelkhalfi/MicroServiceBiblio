<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg" style="background: -webkit-linear-gradient(bottom, #2dbd6e, #a6f77b); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#top" style="font-family: 'Raleway SemiBold', sans-serif;">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border: none;">
            <span class="navbar-toggler-icon" style="background-color: white;"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($_SESSION['user_role'] === 'ADMIN'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../../admin/app/admin_dashboard.php" style="font-family: 'Raleway', sans-serif;">Admin Dashboard</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="user_dashboard.php" style="font-family: 'Raleway', sans-serif;">User Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../../user/app/user_profile.php" style="font-family: 'Raleway', sans-serif;">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../../user/app/user_notifications.php" style="font-family: 'Raleway', sans-serif;">Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../../user/app/user_historique.php" style="font-family: 'Raleway', sans-serif;">Historique</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="navbar-text text-white" style="font-family: 'Raleway SemiBold', sans-serif;">
                        Bienvenue, <?php echo htmlspecialchars($_SESSION['user_nom'] ?? ''); ?>
                    </span>
                </li>
                <li class="nav-item" style="margin-left: 20px;">
                    <a class="navbar-text text-white" href="../../user/app/logout.php" style="font-family: 'Raleway SemiBold', sans-serif;">Se déconnecter</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
