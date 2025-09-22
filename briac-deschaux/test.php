<?php
// On définit le fuseau horaire
date_default_timezone_set('Europe/Paris');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Date et Heure en Temps Réel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
            font-size: 2em;
        }
        #clock {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <h1>Date et Heure en Temps Réel</h1>
    <div id="clock">
        <?php echo date("d/m/Y H:i:s"); ?>
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

        setInterval(updateClock, 1000); // mise à jour toutes les secondes
        updateClock(); // appel initial
    </script>
</body>
</html>