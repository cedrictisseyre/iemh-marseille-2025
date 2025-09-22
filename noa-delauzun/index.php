<?php
// Démonstration des capacités de PHP

echo "<h1>Bienvenue sur la démonstration PHP !</h1>";

echo "<h2>Variables et opérations</h2>";
$a = 5;
$b = 3;
echo "a = $a, b = $b<br>";
echo "a + b = " . ($a + $b) . "<br>";
echo "a * b = " . ($a * $b) . "<br>";

echo "<h2>Boucle for</h2>";
for ($i = 1; $i <= 5; $i++) {
    echo "Itération $i<br>";
}

echo "<h2>Tableau associatif</h2>";
$personne = [
    'nom' => 'Dupont',
    'prénom' => 'Marie',
    'âge' => 28
];
foreach ($personne as $cle => $valeur) {
    echo "$cle : $valeur<br>";
}

echo "<h2>Date et heure actuelles</h2>";
echo date('d/m/Y H:i:s');

?>
