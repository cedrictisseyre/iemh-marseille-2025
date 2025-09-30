<?php
session_start();

$mots = [
    "football", "basket", "tennis", "rugby", "natation", "handball", "volley", "cyclisme", "boxe", "judo",
    "karate", "escrime", "golf", "surf", "ski", "snowboard", "athletisme", "marathon", "triathlon", "badminton",
    "pingpong", "voile", "canoe", "kayak", "plongee", "equit", "danse", "gymnastique", "haltérophilie", "course",
    "patinage", "tir", "lutte", "cricket", "baseball", "softball", "hockey", "curling", "biathlon", "escalade",
    "parkour", "taekwondo", "sambo", "sumo", "pétanque", "bowling", "squash", "polo", "rafting", "bmx"
];

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

include 'jeu.html.php';