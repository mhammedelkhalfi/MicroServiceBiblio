<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Bibliothèque</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
        <div class="container">
            <a class="navbar-brand" href="#">E-Bibliothèque</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#accueil">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="accueil" class="bg-primary text-white text-center py-5">
        <div class="container">
            <h1 class="display-4">Bienvenue dans votre E-Bibliothèque</h1>
            <p class="lead">Un accès instantané à une large collection de livres numériques.</p>
            <a href="types.php" class="btn btn-light btn-lg">Découvrir les catégories</a>
        </div>
    </header>

    <!-- Categories Section -->
    <section id="categories" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Nos Catégories</h2>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm h-100">
                        <img src="images/Romans.jpg" class="card-img-top" alt="Romans">
                        <div class="card-body text-center">
                            <h5 class="card-title">Romans</h5>
                            <p class="card-text">Plongez dans des histoires captivantes et émouvantes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm h-100">
                        <img src="images/science.jpg" class="card-img-top" alt="Science">
                        <div class="card-body text-center">
                            <h5 class="card-title">Science</h5>
                            <p class="card-text">Explorez les mystères de la science et de la technologie.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm h-100">
                        <img src="images/Histoire.jpg" class="card-img-top" alt="Histoire">
                        <div class="card-body text-center">
                            <h5 class="card-title">Histoire</h5>
                            <p class="card-text">Découvrez les grands événements qui ont façonné notre monde.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card shadow-sm h-100">
                        <img src="images/Art.jpg" class="card-img-top" alt="Art">
                        <div class="card-body text-center">
                            <h5 class="card-title">Art</h5>
                            <p class="card-text">Inspirez-vous des chefs-d'œuvre de l'art et de la culture.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Contactez-nous</h2>
            <div class="row">
                <div class="col-md-6">
                    <p>Email : contact@ebibliotheque.com</p>
                    <p>Téléphone : +33 1 23 45 67 89</p>
                </div>
                <div class="col-md-6 text-center">
                    <button id="contact-btn" class="btn btn-primary btn-lg">Envoyer un message</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 E-Bibliothèque. Tous droits réservés.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom Script -->
    <script src="script.js"></script>
</body>
</html>
