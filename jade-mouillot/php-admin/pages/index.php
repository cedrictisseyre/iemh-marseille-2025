<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu Principal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: left;
        }
        .menu a {
            display: inline-block;
            margin: 5px 0;
            padding: 10px 15px;
            background: #2980b9;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        .menu a:hover {
            background: #1abc9c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Menu Principal</h1>
        <div class="menu">
            <a href="gestion_sportif.php">Gestion des Sportifs</a><br>
            <a href="gestion_club.php">Gestion des Clubs</a><br>
            <a href="gestion_course.php">Gestion des Courses</a><br>
            <a href="gestion_discipline.php">Gestion des Disciplines</a><br>
            <a href="gestion_participation.php">Gestion des Participations</a>
        </div>
    </div>
</body>
</html>
