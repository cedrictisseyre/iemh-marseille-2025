<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Gestion sportive</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: left;
        }
        .menu {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .menu a {
            display: inline-block;
            padding: 10px 20px;
            background: #2980b9;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .menu a:hover {
            background: #1abc9c;
        }
        p {
            font-size: 1.2em;
            color: #333;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Bienvenue sur le site de gestion des sportifs, clubs et courses en France</h1>

    <div class="menu">
        <a href="gestion_sportif.php">Sportifs</a>
        <a href="gestion_club.php">Clubs</a>
        <a href="gestion_course.php">Courses</a>
        <a href="gestion_discipline.php">Disciplines</a>
        <a href="gestion_participation.php">Participations</a>
    </div>

    <p>Utilisez les boutons ci-dessus pour accéder aux différentes sections de gestion. Toutes les données sont affichées à gauche pour une lisibilité optimale.</p>
</div>
</body>
</html>
