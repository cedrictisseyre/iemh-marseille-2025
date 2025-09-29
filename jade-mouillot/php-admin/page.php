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
                <?php
                $adminDir = __DIR__;
                $files = scandir($adminDir);
                $menu = [];
                foreach ($files as $file) {
                    if (preg_match('/^(ajout|liste)_(.+)\.php$/', $file, $matches)) {
                        $type = $matches[1] === 'ajout' ? 'Ajouter' : 'Lister';
                        $label = ucfirst(str_replace('_', ' ', $matches[2]));
                        $menu[] = [
                            'href' => $file,
                            'text' => $type . ' ' . $label
                        ];
                    }
                }
                // Ajouter les pages dynamiques principales
                $pagesDynamiques = [
                    ['href' => 'sportif.php', 'text' => 'Sportif (ajout + liste)'],
                    ['href' => 'club.php', 'text' => 'Club (ajout + liste)'],
                    ['href' => 'course.php', 'text' => 'Course (ajout + liste)'],
                    ['href' => 'discipline.php', 'text' => 'Discipline (ajout + liste)'],
                ];
                foreach ($pagesDynamiques as $item) {
                    echo '<a href="' . htmlspecialchars($item['href']) . '">' . htmlspecialchars($item['text']) . '</a>';
                }
                // Tri pour afficher "Ajouter ..." avant "Lister ..."
                usort($menu, function($a, $b) {
                    return strcmp($a['text'], $b['text']);
                });
                foreach ($menu as $item) {
                    echo '<a href="' . htmlspecialchars($item['href']) . '">' . htmlspecialchars($item['text']) . '</a>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>Bienvenue sur la page d'accueil</h1>
        <p>Utilisez le menu pour ajouter des sportifs, des clubs, des courses ou des disciplines.</p>
    </div>
</body>
</html>
