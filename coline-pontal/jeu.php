<?php
session_start();

$mots = [
    "abricot", "banane", "cerise", "datte", "figue", "groseille", "kiwi", "lime", "mangue", "nectarine",
    "orange", "papaye", "pêche", "poire", "prune", "quinoa", "radis", "safran", "tomate", "ugli",
    "vanille", "wasabi", "xérès", "yam", "zeste", "ananas", "brocoli", "carotte", "daikon", "endive",
    "fenouil", "goyave", "haricot", "igname", "jalapeño", "kumquat", "litchi", "maïs", "navet", "olive",
    "pistache", "quetsche", "rhubarbe", "sésame", "tamarin", "urucum", "verveine", "wakamé", "xigua", "yuzu"
];

if (!isset($_SESSION['mot'])) {
    $_SESSION['mot'] = $mots[array_rand($mots)];
    $_SESSION['lettres'] = [];
    $_SESSION['erreurs'] = 0;
    $_SESSION['max_erreurs'] = 8;
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
    $message = $gagne ? "Bravo, vous avez gagné ! Le mot était : {$_SESSION['mot']}" : "Dommage, vous avez perdu. Le mot était : {$_SESSION['mot']}";
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jeu du Pendu</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        .mot { font-size: 2em; letter-spacing: 0.5em; }
        .lettres { margin: 1em 0; }
        .erreurs { color: red; }
    </style>
</head>
<body>
    <h1>Jeu du Pendu</h1>
    <?php if (isset($message)): ?>
        <p><strong><?= $message ?></strong></p>
        <form method="post">
            <button type="submit">Rejouer</button>
        </form>
    <?php else: ?>
        <div class="mot"><?= $mot_affiche ?></div>
        <div class="lettres">
            Lettres proposées : <?= implode(', ', $_SESSION['lettres']) ?>
        </div>
        <div class="erreurs">
            Erreurs : <?= $_SESSION['erreurs'] ?> / <?= $_SESSION['max_erreurs'] ?>
        </div>
        <form method="post">
            <input type="text" name="lettre" maxlength="1" required autofocus>
            <button type="submit">Proposer</button>
        </form>
    <?php endif; ?>
</body>
</html>