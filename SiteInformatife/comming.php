<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #00b4d8, #03045e);
            color: white;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: bold;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .countdown {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .btn {
            font-size: 1rem;
            padding: 10px 25px;
            border-radius: 5px;
            text-transform: uppercase;
        }
        .btn i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-clock"></i> Coming Soon</h1>
        <p>Notre application de location arrive dans :</p>
        <div id="countdown" class="countdown"></div>
        <a href="types.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- JavaScript pour le compte à rebours -->
    <script>
        const launchDate = new Date();
        launchDate.setDate(launchDate.getDate() + 10);

        function updateCountdown() {
            const now = new Date().getTime();
            const timeLeft = launchDate - now;

            if (timeLeft <= 0) {
                document.getElementById('countdown').innerText = "Nous sommes en ligne maintenant !";
                return;
            }

            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            document.getElementById('countdown').innerText =
                `${days} jours ${hours}h ${minutes}m ${seconds}s`;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
