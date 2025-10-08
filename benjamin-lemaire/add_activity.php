<?php
require_once 'config.php';

// Récupération des sports et utilisateurs pour les listes déroulantes
$sports = $pdo->query("SELECT id_sport, nom_sport FROM sports ORDER BY nom_sport")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id_utilisateur, nom FROM utilisateurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $temps = $_POST['temps'] ?? '';
    $id_utilisateur = $_POST['id_utilisateur'] ?? '';
    $id_sport = $_POST['id_sport'] ?? '';
    if ($date && $temps && $id_utilisateur && $id_sport) {
        try {
            // Récupérer le nom du sport à partir de l'id_sport
            $sportName = $pdo->prepare("SELECT nom_sport FROM sports WHERE id_sport = ?");
            $sportName->execute([$id_sport]);
            $sport = $sportName->fetchColumn();
            if (!$sport) {
                throw new Exception('Sport inconnu');
            }
            $stmt = $pdo->prepare("INSERT INTO activites_sportives (date, temps, id_utilisateur, id_sport, sport) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$date, $temps, $id_utilisateur, $id_sport, $sport]);
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $error = 'Erreur lors de l\'insertion : ' . $e->getMessage();
            $debug = [
                'date' => $date,
                'temps' => $temps,
                'id_utilisateur' => $id_utilisateur,
                'id_sport' => $id_sport,
                'sport' => $sport ?? null
            ];
        }
    } else {
        $error = 'Tous les champs sont obligatoires.';
        $debug = [
            'date' => $date,
            'temps' => $temps,
            'id_utilisateur' => $id_utilisateur,
            'id_sport' => $id_sport
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une activité</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Ajouter une activité sportive</h1>
    <?php if (!empty($error)) : ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php if (!empty($debug)) : ?>
            <pre><?= print_r($debug, true) ?></pre>
        <?php endif; ?>
    <?php endif; ?>
    <form method="post">
        <label>Date : <input type="date" name="date" required></label><br>
        <label>Temps (min) : <input type="number" name="temps" min="1" required></label><br>
        <label>Utilisateur :
            <select name="id_utilisateur" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($users as $u) : ?>
                    <option value="<?= $u['id_utilisateur'] ?>"><?= htmlspecialchars($u['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Sport :
            <select name="id_sport" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($sports as $s) : ?>
                    <option value="<?= $s['id_sport'] ?>"><?= htmlspecialchars($s['nom_sport']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Ajouter</button>
    </form>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>
