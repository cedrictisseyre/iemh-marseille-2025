<?php
// Connexion à la base de données
$host = '195.15.235.20';
$user = 'root';
$password = 'INNnsk40374';
$dbname = 'Mathis_Baffico';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Fonction pour créer un utilisateur ou récupérer son id
function getUtilisateurId($conn, $nom, $sexe, $age, $taille, $poids) {
    // Vérifier si l'utilisateur existe déjà (nom + age)
    $nom_safe = $conn->real_escape_string($nom);
    $sql = "SELECT id FROM utilisateurs WHERE nom='$nom_safe' AND age=$age LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {
        // Ajouter l'utilisateur
        $sql_insert = "INSERT INTO utilisateurs (nom, sexe, age, taille, poids) 
                       VALUES ('$nom_safe', '$sexe', $age, $taille, $poids)";
        if ($conn->query($sql_insert) === TRUE) {
            return $conn->insert_id;
        } else {
            die("Erreur lors de l'ajout de l'utilisateur : " . $conn->error);
        }
    }
}

// Traitement du formulaire
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

        // Enregistrement dans la base de données
        $utilisateur_id = getUtilisateurId($conn, $nom, $sexe_affiche, $age, $taille, $poids);

        $sql_calc = "INSERT INTO calculs (utilisateur_id, nap, niveau_activite, metabolisme_base, dej)
                     VALUES ($utilisateur_id, $nap, '$niveau_activite', $mb, $dej)";
        if ($conn->query($sql_calc) === TRUE) {
            $resultat_enregistre = true;
        } else {
            echo "<p style='color:red'>Erreur lors de l'enregistrement du calcul : " . $conn->error . "</p>";
        }

        // Affichage du résultat
        echo '<div class="result">';
        echo '<h2>Résultat :</h2>';
        echo "Nom : $nom<br>";
        echo "Sexe : $sexe_affiche<br>";
        echo "Âge : $age ans<br>";
        echo "Taille : $taille cm<br>";
        echo "Poids : $poids kg<br>";
        echo "Niveau d\'activité : $niveau_activite (NAP = $nap)<br>";
        echo "Métabolisme de base estimé : " . round($mb, 2) . " kcal/jour<br>";
        echo "<strong>Dépense énergétique journalière estimée : " . round($dej, 2) . " kcal/jour</strong>";
        if ($resultat_enregistre) echo "<p style='color:green'>Résultat enregistré en base ✅</p>";
        echo '</div>';
    } else {
        echo '<div class="result" style="background:#ffe0e0;">Veuillez remplir correctement tous les champs.</div>';
    }
}
?>
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
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required>

    <label for="sexe">Sexe :</label>
    <select name="sexe" id="sexe" required>
        <option value="">--Choisissez--</option>
        <option value="homme" <?= (isset($_POST['sexe']) && $_POST['sexe'] == 'homme') ? 'selected' : '' ?>>Homme</option>
        <option value="femme" <?= (isset($_POST['sexe']) && $_POST['sexe'] == 'femme') ? 'selected' : '' ?>>Femme</option>
    </select>

    <label for="age">Âge :</label>
    <input type="number" name="age" id="age" min="0" max="120" value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>" required>

    <label for="taille">Taille (cm) :</label>
    <input type="number" name="taille" id="taille" min="50" max="250" value="<?= isset($_POST['taille']) ? htmlspecialchars($_POST['taille']) : '' ?>" required>

    <label for="poids">Poids (kg) :</label>
    <input type="number" name="poids" id="poids" min="20" max="300" step="0.1" value="<?= isset($_POST['poids']) ? htmlspecialchars($_POST['poids']) : '' ?>" required>

    <label for="nap">Niveau d'activité physique :</label>
    <select name="nap" id="nap" required>
        <option value="">--Choisissez--</option>
        <option value="1.2" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.2') ? 'selected' : '' ?>>Sédentaire (NAP = 1.2)</option>
        <option value="1.375" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.375') ? 'selected' : '' ?>>Activité légère (NAP = 1.375)</option>
        <option value="1.55" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.55') ? 'selected' : '' ?>>Activité modérée (NAP = 1.55)</option>
        <option value="1.725" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.725') ? 'selected' : '' ?>>Activité intense (NAP = 1.725)</option>
        <option value="1.9" <?= (isset($_POST['nap']) && $_POST['nap'] == '1.9') ? 'selected' : '' ?>>Activité très intense (NAP = 1.9)</option>
    </select>

    <button type="submit">Calculer</button>
</form>
</body>
</html>