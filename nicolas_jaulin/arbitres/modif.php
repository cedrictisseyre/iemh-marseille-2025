$php_debug = true;
if ($php_debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
require_once '../db_connect.php';
$message = '';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Arbitres WHERE id_arbitre = ?');
    $stmt->execute([$id]);
    $arbitre = $stmt->fetch();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $nationalite = $_POST['nationalite'] ?? '';
        if ($nom) {
            $stmt = $pdo->prepare('UPDATE Arbitres SET nom = ?, prenom = ?, nationalite = ? WHERE id_arbitre = ?');
            $stmt->execute([$nom, $prenom, $nationalite, $id]);
            $message = 'Arbitre modifié !';
        } else {
            $message = 'Le nom est obligatoire.';
        }
    }
} else {
    header('Location: ../arbitres/liste.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un arbitre</title>
</head>
    <link rel="stylesheet" href="../style-top14.css">
<body>
    <h1>Modifier un arbitre</h1>
    <form method="post">
        <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($arbitre['nom']) ?>" required></label><br>
        <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($arbitre['prenom']) ?>"></label><br>
        <label>Nationalité : <input type="text" name="nationalite" value="<?= htmlspecialchars($arbitre['nationalite']) ?>"></label><br>
        <button type="submit">Modifier</button>
    </form>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="../arbitres/liste.php">Retour à la liste</a>
</body>
</html>