<?php
session_start();

// Liste de 50 mots sur le thème du sport
$mots = [
    "football", "basket", "tennis", "rugby", "natation", "handball", "volley", "cyclisme", "boxe", "judo",
    "karate", "escrime", "golf", "surf", "ski", "snowboard", "athletisme", "marathon", "triathlon", "badminton",
    "pingpong", "voile", "canoe", "kayak", "plongee", "equit", "danse", "gymnastique", "haltérophilie", "course",
    "patinage", "tir", "lutte", "cricket", "baseball", "softball", "hockey", "curling", "biathlon", "escalade",
    "parkour", "taekwondo", "sambo", "sumo", "pétanque", "bowling", "squash", "polo", "rafting", "bmx"
];

// Dessins du pendu (ASCII art)
$pendu = [
    "
     _______
    |/      
    |       
    |       
    |       
    |       
    |___    
    ",
    "
     _______
    |/      |
    |       
    |       
    |       
    |       
    |___    
    ",
    "
     _______
    |/      |
    |      (_)
    |       
    |       
    |       
    |___    
    ",
    "
     _______
    |/      |
    |      (_)
    |       |
    |       
    |       
    |___    
    ",
    "
     _______
    |/      |
    |      (_)
    |      /|
    |       
    |       
    |___    
    ",
    "
     _______
    |/      |
    |      (_)
    |      /|\\
    |       
    |       
    |___    
    ",
    "
     _______
    |/      |
    |      (_)
    |      /|\\
    |      / 
    |       
    |___    
    ",
    "
     _______
    |/      |
    |      (_)
    |      /|\\
    |      / \\
    |       
    |___    
    "
];

// Initialisation de la partie
if (!isset($_SESSION['mot'])) {
    $_SESSION['mot'] = $mots[array_rand($mots)];
    $_SESSION['lettres'] = [];
    $_SESSION['erreurs'] = 0;
    $_SESSION['max_erreurs'] = count($pendu) - 1;
}

if (isset($_POST['lettre'])) {
    $lettre = strtolower($_POST['lettre']);
    if (!in_array($lettre, $_SESSION['lettres']) && ctype_alpha($lettre) && strlen($lettre) == 1) {
        $_SESSION['lettres'][] = $lettre;
        if (strpos($_SESSION['mot'], $lettre) === false) {
            $_SESSION['erreurs']++;
        }
    }
}

$mot_affiche = '';
foreach (str_split($_SESSION['mot']) as $l) {
    $mot_affiche .= in_array($l, $_SESSION['lettres']) ? $l : '_';
}

$gagne = ($mot_affiche == $_SESSION['mot']);
$perdu = ($_SESSION['erreurs'] >= $_SESSION['max_erreurs']);

if ($gagne || $perdu) {
    if ($gagne) {
        $message = "Bravo, vous avez gagné ! Le mot était : <span class='mot-gagne'>{$_SESSION['mot']}</span>";
    } else {
        $message = "Perdu retourne t'entrainer le plouc<br>Le mot était : <span class='mot-perdu'>{$_SESSION['mot']}</span>";
    }
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jeu du Pendu Sportif</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 420px;
            margin: 3em auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2em;
            text-align: center;
        }
        h1 {
            color: #2563eb;
            margin-bottom: 1em;
        }
        .mot {
            font-size: 2.2em;
            letter-spacing: 0.5em;
            margin: 1em 0;
            font-family: 'Fira Mono', monospace;
        }
        .lettres {
            margin: 1em 0;
            color: #64748b;
        }
        .erreurs {
            color: #ef4444;
            font-weight: bold;
            margin-bottom: 1em;
        }
        input[type="text"] {
            font-size: 1.2em;
            padding: 0.4em;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            width: 60px;
            text-align: center;
        }
        button {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.6em 1.2em;
            font-size: 1em;
            cursor: pointer;
            margin-left: 0.5em;
            transition: background 0.2s;
        }
        button:hover {
            background: #1e40af;
        }
        .mot-gagne {
            color: #22c55e;
            font-weight: bold;
        }
        .mot-perdu {
            color: #ef4444;
            font-weight: bold;
        }
        .pendu {
            font-family: monospace;
            white-space: pre;
            font-size: 1em;
            color: #334155;
            margin-bottom: 1em;
        }
        .rejouer-btn {
            margin-top: 1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Jeu du Pendu Sportif</h1>
        <div class="pendu">
            <?= $perdu ? $pendu[$_SESSION['max_erreurs']] : $pendu[$_SESSION['erreurs']] ?>
        </div>
        <?php if (isset($message)): ?>
            <p><strong><?= $message ?></strong></p>
            <form method="post" class="rejouer-btn">
                <button type="submit">Rejouer</button>
            </form>
        <?php else: ?>
            <div class="mot"><?= $mot_affiche ?></div>
            <div class="lettres">
                Lettres proposées : <?= empty($_SESSION['lettres']) ? 'Aucune' : implode(', ', $_SESSION['lettres']) ?>
            </div>
            <div class="erreurs">
                Erreurs : <?= $_SESSION['erreurs'] ?> / <?= $_SESSION['max_erreurs'] ?>
            </div>
            <form method="post">
                <input type="text" name="lettre" maxlength="1" required autofocus autocomplete="off" pattern="[A-Za-zÀ-ÿ]">
                <button type="submit">Proposer</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>