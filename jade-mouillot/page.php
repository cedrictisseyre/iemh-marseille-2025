<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Gestion Sportive</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #2c3e50;
            padding: 1em;
            color: #fff;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropbtn {
            background: #2980b9;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: #2c3e50;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background: #e1e1e1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown:hover .dropbtn {
            background: #1abc9c;
        }
        .container {
            margin: 40px auto;
            max-width: 600px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="dropdown">
            <button class="dropbtn">Menu</button>
            <div class="dropdown-content">
                <a href="pages/ajouter_sportif.html">Ajouter sportif</a>
                <a href="pages/ajouter_club.html">Ajouter club</a>
                <a href="pages/ajouter_course.html">Ajouter course</a>
                <a href="pages/ajouter_discipline.html">Ajouter discipline</a>
                <a href="pages/afficher_sportif.html">Lister sportifs</a>
                <a href="pages/afficher_club.html">Lister Clubs</a>
                <a href="pages/afficher_course.html">Lister Courses</a>
                <a href="pages/afficher_discipline.html">Lister Discipline</a>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>Bienvenue sur la page d'accueil</h1>
        <p>Utilisez le menu pour ajouter des sportifs, des clubs, des courses ou des disciplines.</p>
    </div>
</body>
</html>
