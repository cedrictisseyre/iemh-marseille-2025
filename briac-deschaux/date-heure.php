<?php
// On définit le fuseau horaire
date_default_timezone_set('Europe/Paris');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Date & Heure en Temps Réel</title>
    <style>
        /* Style global */
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #ecf0f1;
        }

        /* Conteneur principal */
        .container {
            text-align: center;
            padding: 40px 60px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 400;
            letter-spacing: 2px;
            color: #00d9ff;
        }

        #clock {
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Date & Heure en Temps Réel</h1>
        <div id="clock">
            <?php echo date("d/m/Y H:i:s"); ?>
        </div>
    </div>

    <script>
        function updateClock() {
            let now = new Date();
            let day   = String(now.getDate()).padStart(2, '0');
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let year  = now.getFullYear();

            let hours   = String(now.getHours()).padStart(2, '0');
            let minutes = String(now.getMinutes()).padStart(2, '0');
            let seconds = String(now.getSeconds()).padStart(2, '0');

            document.getElementById('clock').innerHTML =
                day + "/" + month + "/" + year + " " + hours + ":" + minutes + ":" + seconds;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>