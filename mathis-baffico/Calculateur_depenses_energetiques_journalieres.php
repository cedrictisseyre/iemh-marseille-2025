<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calculateur des dépenses énergétiques journalières</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        form { background: #f5f5f5; padding: 1.5em; border-radius: 8px; max-width: 400px; }
        label { display: block; margin-top: 1em; }
        input, select { width: 100%; padding: 0.5em; margin-top: 0.2em; }
        .result { background: #e0ffe0; padding: 1em; border-radius: 8px; margin-top: 2em; }
    </style>
</head>
<body>
    <h1>Calculateur des dépenses énergétiques journalières</h1>
    <form method="post">
        <label for="sexe">Sexe :</label>
        <select name="sexe" id="sexe" required>
            <option value="">--Choisissez--</option>
            <option value="homme" <?= (isset($_POST['sexe']) && $_POST['sexe'] == 'homme') ? 'selected' : '' ?>>Homme</option>
            <option value="femme" <?= (isset($_POST['sexe']) && $_POST['sexe'] == 'femme') ? 'selected' : '' ?>>Femme</option>
        </select>

        <label for="age">Âge (en années) :</label>
        <input type="number" name="age" id="age" min="0" max="120" value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>" required>

        <label for="taille">Taille (en cm) :</label>
        <input type="number" name="taille" id="taille" min="50" max="250" value="<?= isset($_POST['taille']) ? htmlspecialchars($_POST['taille']) : '' ?>" required>

        <label for="poids">Poids (en kg) :</label>
        <input type="number" name="poids" id="poids" min="20" max="300" step="0.1" value="<?= isset($_POST['poids']) ? htmlspecialchars($_POST['poids']) : '' ?>" required>

        <label for="nap">Niveau d'activité physique :</label>
        <select name="nap" id="nap" required>
            <option value="">--Choisissez--</option>
            <option value="1.2" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.2') ? 'selected' : '' ?>>Sédentaire (NAP = 1.2) - Peu ou pas d'exercice</option>
            <option value="1.375" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.375') ? 'selected' : '' ?>>Activité légère (NAP = 1.375) - Marche, tâches ménagères</option>
            <option value="1.55" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.55') ? 'selected' : '' ?>>Activité modérée (NAP = 1.55) - Sport modéré, travail debout</option>
            <option value="1.725" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.725') ? 'selected' : '' ?>>Activité intense (NAP = 1.725) - Sport intense, travail physique</option>
            <option value="1.9" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.9') ? 'selected' : '' ?>>Activité très intense (NAP = 1.9) - Athlète, travail très physique</option>
        </select>

        <button type="submit">Calculer</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sexe = strtolower(trim($_POST['sexe'] ?? ''));
        $age = (int)($_POST['age'] ?? 0);
        $taille = (float)($_POST['taille'] ?? 0);
        $poids = (float)($_POST['poids'] ?? 0);
        $nap = (float)($_POST['nap'] ?? 0);

        $niveau_activite = '';
        switch ($nap) {
            case 1.2:
                $niveau_activite = "Sédentaire";
                break;
            case 1.375:
                $niveau_activite = "Activité légère";
                break;
            case 1.55:
                $niveau_activite = "Activité modérée";
                break;
            case 1.725:
                $niveau_activite = "Activité intense";
                break;
            case 1.9:
                $niveau_activite = "Activité très intense";
                break;
        }

        if ($sexe === "homme") {
            $mb = (10 * $poids) + (6.25 * $taille) - (5 * $age) + 5;
            $sexe_affiche = "Homme";
        } elseif ($sexe === "femme") {
            $mb = (10 * $poids) + (6.25 * $taille) - (5 * $age) - 161;
            $sexe_affiche = "Femme";
        } else {
            $mb = null;
            $sexe_affiche = "Non reconnu";
        }

        if ($mb !== null && $nap > 0 && $age > 0 && $taille > 0 && $poids > 0) {
            $dej = $mb * $nap;
            echo '<div class="result">';
            echo '<h2>Résultat :</h2>';
            echo "Sexe : $sexe_affiche<br>";
            echo "Âge : $age ans<br>";
            echo "Taille : $taille cm<br>";
            echo "Poids : $poids kg<br>";
            echo "Niveau d\'activité : $niveau_activite (NAP = $nap)<br>";
            echo "Métabolisme de base estimé (Mifflin-St Jeor 1990) : " . round($mb, 2) . " kcal/jour<br>";
            echo "<strong>Dépense énergétique journalière estimée : " . round($dej, 2) . " kcal/jour</strong>";
            echo '</div>';
        } else {
            echo '<div class="result" style="background:#ffe0e0;">Veuillez remplir correctement tous les champs.</div>';
        }
    }
    ?>
</body>
</html>
