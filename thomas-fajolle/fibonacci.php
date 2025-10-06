<?php
// Calcul de la suite de Fibonacci
function fibonacci($n) {
    if ($n <= 0) return [];
    if ($n == 1) return [0];
    $fib = [0, 1];
    for ($i = 2; $i < $n; $i++) {
        $fib[] = $fib[$i - 1] + $fib[$i - 2];
    }
    return $fib;
}

// Exemple d'utilisation : afficher les 10 premiers termes
$nombre_termes = 10;
$fib_sequence = fibonacci($nombre_termes);
echo "Suite de Fibonacci (" . $nombre_termes . " termes) :\n";
echo implode(", ", $fib_sequence);
