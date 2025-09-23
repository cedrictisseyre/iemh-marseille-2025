<?php
// math_tools.php
// Librairie + petite UI en web pour faire beaucoup de calculs mathématiques en PHP.
// Sauvegarde sous math_tools.php et ouvre dans ton navigateur.
// Encodage UTF-8 recommandé.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -----------------------------
// Fonctions utilitaires
// -----------------------------

function safe_div($a, $b) {
    if ($b == 0) throw new Exception("Division par zéro.");
    return $a / $b;
}

function is_integerish($x) {
    return is_numeric($x) && floor($x) == $x;
}

// Factorielle (avec BCMath si nécessaire pour grands nombres)
function factorial($n) {
    if ($n < 0) throw new Exception("Factorielle non définie pour n < 0");
    if (!is_integerish($n)) throw new Exception("Factorielle attend un entier.");
    $n = (int)$n;
    if ($n <= 1) return 1;
    // Si BCMath disponible et n>20 on peut l'utiliser pour précision arbitr.
    if (extension_loaded('bcmath')) {
        $res = '1';
        for ($i=2; $i<=$n; $i++) $res = bcmul($res, (string)$i);
        return $res;
    } else {
        $res = 1;
        for ($i=2; $i<=$n; $i++) $res *= $i;
        return $res;
    }
}

// Permutation nPr et combinaison nCr (avec protection)
function nPr($n, $r) {
    if (!is_integerish($n) || !is_integerish($r)) throw new Exception("n et r doivent être entiers.");
    if ($r < 0 || $n < 0) throw new Exception("n et r doivent être >= 0.");
    if ($r > $n) return 0;
    $res = 1;
    for ($i=0; $i<$r; $i++) $res *= ($n - $i);
    return $res;
}
function nCr($n, $r) {
    if (!is_integerish($n) || !is_integerish($r)) throw new Exception("n et r doivent être entiers.");
    if ($r < 0 || $n < 0) throw new Exception("n et r doivent être >= 0.");
    if ($r > $n) return 0;
    $r = min($r, $n - $r);
    if ($r == 0) return 1;
    $num = 1;
    $den = 1;
    for ($i=1; $i <= $r; $i++) {
        $num *= ($n - $r + $i);
        $den *= $i;
    }
    return intdiv($num, $den);
}

// PGCD (Euclide) et PPCM
function gcd($a, $b) {
    $a = abs((int)$a); $b = abs((int)$b);
    if ($a == 0) return $b;
    if ($b == 0) return $a;
    while ($b) {
        $t = $b;
        $b = $a % $b;
        $a = $t;
    }
    return $a;
}
function lcm($a, $b) {
    if ($a == 0 || $b == 0) return 0;
    return abs(intdiv($a * $b, gcd($a, $b)));
}

// Test de primalité (simple, deterministe pour petits n)
function is_prime($n) {
    if ($n <= 1) return false;
    if ($n <= 3) return true;
    if ($n % 2 == 0) return false;
    $r = (int)floor(sqrt($n));
    for ($i = 3; $i <= $r; $i += 2) {
        if ($n % $i == 0) return false;
    }
    return true;
}

// Factorisation en nombres premiers (algorithme simple)
function prime_factors($n) {
    $n = abs((int)$n);
    $factors = [];
    if ($n < 2) return $factors;
    while ($n % 2 == 0) { $factors[] = 2; $n /= 2; }
    $p = 3;
    while ($p * $p <= $n) {
        while ($n % $p == 0) { $factors[] = $p; $n /= $p; }
        $p += 2;
    }
    if ($n > 1) $factors[] = $n;
    return $factors;
}

// Moyenne, médiane, mode, variance, écart-type
function mean(array $arr) {
    if (count($arr) == 0) return null;
    return array_sum($arr) / count($arr);
}
function median(array $arr) {
    $n = count($arr);
    if ($n == 0) return null;
    sort($arr);
    $mid = intdiv($n, 2);
    if ($n % 2 == 1) return $arr[$mid];
    return ($arr[$mid-1] + $arr[$mid]) / 2;
}
function mode(array $arr) {
    if (count($arr) == 0) return null;
    $counts = array_count_values($arr);
    arsort($counts);
    $max = reset($counts);
    $modes = array_keys($counts, $max);
    return (count($modes) == 1) ? $modes[0] : $modes;
}
function variance(array $arr, $population = true) {
    $n = count($arr);
    if ($n == 0) return null;
    $m = mean($arr);
    $sum = 0;
    foreach ($arr as $v) $sum += pow($v - $m, 2);
    return $population ? ($sum / $n) : ($sum / max(1, $n - 1));
}
function stddev(array $arr, $population = true) {
    $v = variance($arr, $population);
    return $v === null ? null : sqrt($v);
}

// Résolution d'équation quadratique ax^2+bx+c=0
function solve_quadratic($a, $b, $c) {
    if ($a == 0) { // équation linéaire bx + c = 0
        if ($b == 0) return ($c == 0) ? ["all_reals"] : [];
        return [-$c / $b];
    }
    $d = $b*$b - 4*$a*$c;
    if ($d > 0) {
        return [(-$b - sqrt($d)) / (2*$a), (-$b + sqrt($d)) / (2*$a)];
    } elseif ($d == 0) {
        return [-$b / (2*$a)];
    } else {
        // racines complexes
        $re = -$b / (2*$a);
        $im = sqrt(-$d) / (2*$a);
        return [[$re, -$im], [$re, $im]]; // [re, im]
    }
}

// Complexes : représente comme [re, im]
function complex_add($a, $b) { return [$a[0]+$b[0], $a[1]+$b[1]]; }
function complex_sub($a, $b) { return [$a[0]-$b[0], $a[1]-$b[1]]; }
function complex_mul($a, $b) { return [$a[0]*$b[0]-$a[1]*$b[1], $a[0]*$b[1]+$a[1]*$b[0]]; }
function complex_div($a, $b) {
    $den = $b[0]*$b[0] + $b[1]*$b[1];
    if ($den == 0) throw new Exception("Division par zéro (complexe).");
    return [($a[0]*$b[0]+$a[1]*$b[1])/$den, ($a[1]*$b[0]-$a[0]*$b[1])/$den];
}
function complex_abs($a) { return sqrt($a[0]*$a[0] + $a[1]*$a[1]); }

// Matrices (2D arrays) : addition et multiplication
function matrix_add(array $A, array $B) {
    $r = count($A); $c = count($A[0] ?? []);
    if ($r != count($B) || $c != count($B[0] ?? [])) throw new Exception("Tailles de matrices incompatibles pour l'addition.");
    $C = [];
    for ($i=0;$i<$r;$i++) {
        for ($j=0;$j<$c;$j++) $C[$i][$j] = $A[$i][$j] + $B[$i][$j];
    }
    return $C;
}
function matrix_mul(array $A, array $B) {
    $ra = count($A); $ca = count($A[0] ?? []);
    $rb = count($B); $cb = count($B[0] ?? []);
    if ($ca != $rb) throw new Exception("Tailles de matrices incompatibles pour la multiplication.");
    $C = array_fill(0, $ra, array_fill(0, $cb, 0));
    for ($i=0;$i<$ra;$i++) {
        for ($j=0;$j<$cb;$j++) {
            $sum = 0;
            for ($k=0;$k<$ca;$k++) $sum += $A[$i][$k] * $B[$k][$j];
            $C[$i][$j] = $sum;
        }
    }
    return $C;
}

// Arithmétique modulaire : inverse modulaire (algorithme d'Euclide étendu)
function egcd($a, $b) {
    if ($b == 0) return [$a, 1, 0];
    list($g, $x1, $y1) = egcd($b, $a % $b);
    return [$g, $y1, $x1 - intdiv($a, $b) * $y1];
}
function modinv($a, $m) {
    list($g, $x, $y) = egcd($a, $m);
    if ($g != 1) throw new Exception("Inverse modulaire n'existe pas (a et m non premiers entre eux).");
    $res = $x % $m;
    if ($res < 0) $res += $m;
    return $res;
}
function modpow($base, $exp, $mod) {
    if ($mod == 1) return 0;
    $res = 1;
    $base = $base % $mod;
    while ($exp > 0) {
        if ($exp % 2 == 1) $res = ($res * $base) % $mod;
        $base = ($base * $base) % $mod;
        $exp = intdiv($exp, 2);
    }
    return $res;
}

// Log, exp, trig, hyperbolic wrappers (PHP built-in)
function safe_log($x, $base = M_E) {
    if ($x <= 0) throw new Exception("Logarithme non défini pour x <= 0.");
    return ($base == M_E) ? log($x) : (log($x) / log($base));
}

// -----------------------------
// Petite UI web pour tester
// -----------------------------
function h($s) { return htmlspecialchars((string)$s); }

$action = $_GET['action'] ?? '';

?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<title>Outils Mathématiques PHP</title>
<style>
body{font-family:Arial;max-width:1000px;margin:20px auto;padding:10px}
section{border:1px solid #ddd;padding:12px;margin-bottom:12px;border-radius:6px}
label{display:block;margin-top:6px}
input[type=text], textarea, select{width:100%;padding:6px;margin-top:4px}
button{padding:8px 12px;margin-top:8px}
pre{background:#f7f7f7;padding:10px;border-radius:6px;overflow:auto}
.two{display:grid;grid-template-columns:1fr 1fr;gap:10px}
</style>
</head>
<body>
<h1>Outils Mathématiques PHP</h1>
<p>Choisis une opération et fournis les paramètres. Le script retourne le résultat ou un message d'erreur.</p>

<section>
<h2>1) Opérations simples</h2>
<form method="post" action="?action=basic">
<label>Opération : <select name="op">
    <option value="add">Addition</option>
    <option value="sub">Soustraction</option>
    <option value="mul">Multiplication</option>
    <option value="div">Division</option>
    <option value="pow">Puissance</option>
    <option value="root">Racine (racine n-ième)</option>
</select></label>
<label>Nombre A : <input type="text" name="a" value=""/></label>
<label>Nombre B (ou exposant / n pour racine) : <input type="text" name="b" value=""/></label>
<button>Calculer</button>
</form>
</section>

<section>
<h2>2) Factorielle / combinatoire</h2>
<form method="post" action="?action=combi">
<label>Action : <select name="act">
    <option value="fact">Factorielle (n!)</option>
    <option value="npr">Permutation nPr</option>
    <option value="ncr">Combinaison nCr</option>
</select></label>
<label>n : <input type="text" name="n" value=""/></label>
<label>r (si applicable) : <input type="text" name="r" value=""/></label>
<button>Calculer</button>
</form>
</section>

<section>
<h2>3) Nombres premiers / factorisation</h2>
<form method="post" action="?action=prime">
<label>Nombre : <input type="text" name="n" value=""/></label>
<label><input type="checkbox" name="doFactors"/> Donner les facteurs premiers</label>
<button>Tester / Factoriser</button>
</form>
</section>

<section>
<h2>4) Statistiques</h2>
<form method="post" action="?action=stats">
<label>Liste de nombres (séparés par virgule) : <input type="text" name="nums" value="1,2,2,3,4"/></label>
<label>Population ou échantillon pour variance/std ? <select name="pop"><option value="1">Population</option><option value="0">Échantillon</option></select></label>
<button>Calculer</button>
</form>
</section>

<section>
<h2>5) Résoudre quadratique</h2>
<form method="post" action="?action=quad">
<label>a : <input type="text" name="a" value="1"/></label>
<label>b : <input type="text" name="b" value="0"/></label>
<label>c : <input type="text" name="c" value="-1"/></label>
<button>Résoudre</button>
</form>
</section>

<section>
<h2>6) Arithmétique modulaire</h2>
<form method="post" action="?action=mod">
<label>Action : <select name="mact">
    <option value="inv">Inverse modulaire</option>
    <option value="powmod">Puissance modulaire</option>
</select></label>
<label>a (ou base) : <input type="text" name="ma" value=""/></label>
<label>b (exp ou modulo) : <input type="text" name="mb" value=""/></label>
<label>m (modulo) : <input type="text" name="mm" value=""/></label>
<button>Calculer</button>
</form>
</section>

<?php
// Traitement des formulaires
try {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'basic') {
        $op = $_POST['op'] ?? 'add';
        $a = (float)$_POST['a'];
        $b = (float)$_POST['b'];
        switch ($op) {
            case 'add': $res = $a + $b; break;
            case 'sub': $res = $a - $b; break;
            case 'mul': $res = $a * $b; break;
            case 'div': $res = safe_div($a, $b); break;
            case 'pow': $res = pow($a, $b); break;
            case 'root':
                if ($b == 0) throw new Exception("Ordre de racine (n) non défini.");
                $res = pow($a, 1.0 / $b);
                break;
            default: throw new Exception("Opération inconnue.");
        }
        echo "<section><h3>Résultat</h3><pre>" . h($res) . "</pre></section>";
    }

    if ($action === 'combi') {
        $act = $_POST['act'] ?? 'fact';
        $n = $_POST['n'];
        $r = $_POST['r'] ?? null;
        if ($act === 'fact') {
            $res = factorial((int)$n);
        } elseif ($act === 'npr') {
            $res = nPr((int)$n, (int)$r);
        } else {
            $res = nCr((int)$n, (int)$r);
        }
        echo "<section><h3>Résultat</h3><pre>" . h($res) . "</pre></section>";
    }

    if ($action === 'prime') {
        $n = (int)$_POST['n'];
        $check = is_prime($n) ? 'Premier' : 'Non premier';
        $out = "Nombre: $n -> $check\n";
        if (isset($_POST['doFactors'])) $out .= "Facteurs: " . implode(',', prime_factors($n));
        echo "<section><h3>Résultat</h3><pre>" . h($out) . "</pre></section>";
    }

    if ($action === 'stats') {
        $raw = $_POST['nums'] ?? '';
        $parts = array_filter(array_map('trim', explode(',', $raw)), fn($v)=>$v!=='' );
        $nums = array_map('floatval', $parts);
        $pop = ($_POST['pop'] ?? '1') == '1';
        $out = "";
        $out .= "N = " . count($nums) . "\n";
        $out .= "Moyenne = " . mean($nums) . "\n";
        $out .= "Médiane = " . median($nums) . "\n";
        $out .= "Mode = " . json_encode(mode($nums)) . "\n";
        $out .= "Variance = " . variance($nums, $pop) . "\n";
        $out .= "Écart-type = " . stddev($nums, $pop) . "\n";
        echo "<section><h3>Résultat</h3><pre>" . h($out) . "</pre></section>";
    }

    if ($action === 'quad') {
        $a = (float)$_POST['a']; $b = (float)$_POST['b']; $c = (float)$_POST['c'];
        $sol = solve_quadratic($a, $b, $c);
        echo "<section><h3>Résultat</h3><pre>" . h(print_r($sol, true)) . "</pre></section>";
    }

    if ($action === 'mod') {
        $mact = $_POST['mact'] ?? '';
        $a = (int)($_POST['ma'] ?? 0);
        $b = (int)($_POST['mb'] ?? 0);
        $m = (int)($_POST['mm'] ?? 0);
        if ($mact === 'inv') {
            $res = modinv($a, $m);
        } else {
            $res = modpow($a, $b, $m);
        }
        echo "<section><h3>Résultat</h3><pre>" . h($res) . "</pre></section>";
    }
}
} catch (Exception $e) {
    echo "<section style='border-color:#f88'><h3>Erreur</h3><pre>" . h($e->getMessage()) . "</pre></section>";
}
?>

<section>
<h2>Exemples d'utilisation / tests rapides</h2>
<pre>
// Factorielle
echo factorial(10); // 3628800

// Combinaisons
echo nCr(10,3); // 120

// PGCD / PPCM
echo gcd(12,18); // 6
echo lcm(12,18); // 36

// Primo test & factorization
is_prime(97); // true
prime_factors(360); // [2,2,2,3,3,5]

// Statistiques
mean([1,2,3]); median([1,2,3,4]); mode([1,1,2,3]);

// Quadratique
solve_quadratic(1,0,-1); // [-1,1]

// Modulaire
modinv(3,11); // 4
modpow(2,10,1000); // 24
</pre>
</section>

</body>
</html>
