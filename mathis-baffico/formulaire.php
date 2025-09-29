<?php
require_once 'connexion.php';

function getUtilisateurId($pdo, $nom, $sexe, $age, $taille, $poids) {
    // Vérifier si l’utilisateur existe déjà (nom + âge)
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE nom = :nom AND age = :age LIMIT 1");
    $stmt->execute(['nom' => $nom, 'age' => $age]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return $row['id'];
    } else {
        // Ajouter l’utilisateur
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, sexe, age, taille, poids) 
                               VALUES (:nom, :sexe, :age, :taille, :poids)");
        $stmt->execute([
            'nom' => $nom,
            'sexe' => $sexe,
            'age' => $age,
            'taille' => $taille,
            'poids' => $poids
        ]);
        return $pdo->lastInsertId();
    }
}

$resultat_enregistre = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? 'Inconnu';
    $sexe = strtolower(trim($_POST['sexe'] ?? ''));
    $age = (int)($_POST['age'] ?? 0);
    $taille = (float)($_POST['taille'] ?? 0);
    $poids = (float)($_POST['poids'] ?? 0);
    $nap = (float)($_POST['nap'] ?? 0);

    $niveau_activite = match ($nap) {
        1.2 => "Sédentaire",
        1.375 => "Activité légère",
        1.55 => "Activité modérée",
        1.725 => "Activité intense",
        1.9 => "Activité très intense",
        default => "Inconnu",
    };

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

        // Enregistrement dans la base
        $utilisateur_id = getUtilisateurId($pdo, $nom, $sexe_affiche, $age, $taille, $poids);

        $stmt = $pdo->prepare("INSERT INTO calculs (utilisateur_id, nap, niveau_activite, metabolisme_base, dej, date_calcul) 
                               VALUES (:utilisateur_id, :nap, :niveau_activite, :mb, :dej, NOW())");
        $resultat_enregistre = $stmt->execute([
            'utilisateur_id' => $utilisateur_id,
            'nap' => $nap,
            'niveau_activite' => $niveau_activite,
            'mb' => $mb,
            'dej' => $dej
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calculateur DEJ</title>
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
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required>

    <label for="sexe">Sexe :</label>
    <select name="sexe" id="sexe" required>
        <option value="">--Choisissez--</option>
        <option value="homme">Homme</option>
        <option value="femme">Femme</option>
    </select>

    <label for="age">Âge :</label>
    <input type="number" name="age" id="age" min="0" max="120" required>

    <label for="taille">Taille (cm) :</label>
    <input type="number" name="taille" id="taille" min="50" max="250" required>

    <label for="poids">Poids (kg) :</label>
    <input type="number" name="poids" id="poids" min="20" max="300" step="0.1" required>

    <label for="nap">Niveau d'activité physique :</label>
    <select name="nap" id="nap" required>
        <option value="">--Choisissez--</option>
        <option value="1.2">Sédentaire (NAP = 1.2)</option>
        <option value="1.375">Activité légère (NAP = 1.375)</option>
        <option value="1.55">Activité modérée (NAP = 1.55)</option>
        <option value="1.725">Activité intense (NAP = 1.725)</option>
        <option value="1.9">Activité très intense (NAP = 1.9)</option>
    </select>

    <button type="submit">Calculer</button>
</form>

<?php if (isset($dej)) : ?>
<div class="result">
    <h2>Résultat :</h2>
    <p>Nom : <?= htmlspecialchars($nom) ?></p>
    <p>Sexe : <?= $sexe_affiche ?></p>
    <p>Âge : <?= $age ?> ans</p>
    <p>Taille : <?= $taille ?> cm</p>
    <p>Poids : <?= $poids ?> kg</p>
    <p>Niveau d'activité : <?= $niveau_activite ?> (NAP = <?= $nap ?>)</p>
    <p>Métabolisme de base : <?= round($mb, 2) ?> kcal/jour</p>
    <p><strong>DEJ estimé : <?= round($dej, 2) ?> kcal/jour</strong></p>
    <?php if ($resultat_enregistre): ?>
        <p style="color:green">Résultat enregistré ✅</p>
    <?php endif; ?>
</div>
<?php endif; ?>
<br>
<a href="affichage.php">Voir l'historique</a>
</body>
</html>