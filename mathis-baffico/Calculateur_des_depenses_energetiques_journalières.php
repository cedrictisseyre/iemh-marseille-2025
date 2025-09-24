<?php
// Calculateur des dépenses énergétiques journalières
// Basé sur la formule de Mifflin-St Jeor (1990)

function demander($message) {
    echo $message;
    return trim(fgets(STDIN));
}

// Demande des informations à l'utilisateur
$sexe = strtolower(demander("Entrez votre sexe (Homme/Femme) : "));
$age = (int)demander("Entrez votre âge (en années) : ");
$taille = (float)demander("Entrez votre taille (en cm) : ");
$poids = (float)demander("Entrez votre poids (en kg) : ");


// Demande du niveau d'activité physique (NAP) avec descriptions détaillées
echo "\nVeuillez choisir votre niveau d'activité (NAP) :\n";
echo "1. Sédentaire (NAP = 1.2) :\n   - Peu ou pas d'exercice, travail de bureau, déplacements quotidiens minimes.\n";
echo "2. Activité légère (NAP = 1.375) :\n   - Activité physique légère (ex : marche 30-45 min/jour), tâches ménagères, petits déplacements.\n";
echo "3. Activité modérée (NAP = 1.55) :\n   - Exercice modéré (ex : 30-60 min de sport/jour, vélo, natation douce), travail debout.\n";
echo "4. Activité intense (NAP = 1.725) :\n   - Exercice intense (ex : 1-2h de sport/jour, entraînement sportif régulier), travail physique.\n";
echo "5. Activité très intense (NAP = 1.9) :\n   - Activité physique très intense (ex : plus de 2h de sport/jour, athlète, travail très physique).\n";
$choix_nap = demander("Entrez le numéro correspondant à votre niveau d'activité : ");

switch ($choix_nap) {
    case '1':
        $nap = 1.2;
        $niveau_activite = "Sédentaire";
        break;
    case '2':
        $nap = 1.375;
        $niveau_activite = "Activité légère";
        break;
    case '3':
        $nap = 1.55;
        $niveau_activite = "Activité modérée";
        break;
    case '4':
        $nap = 1.725;
        $niveau_activite = "Activité intense";
        break;
    case '5':
        $nap = 1.9;
        $niveau_activite = "Activité très intense";
        break;
    default:
        echo "Niveau d'activité non reconnu.\n";
        exit(1);
}

if ($sexe === "homme") {
    $mb = (10 * $poids) + (6.25 * $taille) - (5 * $age) + 5;
    $sexe_affiche = "Homme";
} elseif ($sexe === "femme") {
    $mb = (10 * $poids) + (6.25 * $taille) - (5 * $age) - 161;
    $sexe_affiche = "Femme";
} else {
    echo "Sexe non reconnu. Veuillez entrer 'Homme' ou 'Femme'.\n";
    exit(1);
}

$dej = $mb * $nap;

echo "\nRésultat :\n";
echo "Sexe : $sexe_affiche\n";
echo "Âge : $age ans\n";
echo "Taille : $taille cm\n";
echo "Poids : $poids kg\n";
echo "Niveau d'activité : $niveau_activite (NAP = $nap)\n";
echo "Métabolisme de base estimé (Mifflin-St Jeor 1990) : " . round($mb, 2) . " kcal/jour\n";
echo "Dépense énergétique journalière estimée : " . round($dej, 2) . " kcal/jour\n";
