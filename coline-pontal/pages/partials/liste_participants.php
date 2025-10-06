<?php
// Liste des participants avec option de suppression
include_once __DIR__ . '/../../includes/db_connexion.php';

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare('DELETE FROM participants WHERE id = ?');
    $stmt->execute([$id]);
    echo '<p style="color:green;">Participant supprimé avec succès.</p>';
}

// Récupérer la liste des participants
$participants = $pdo->query('SELECT id, nom, prenom, grade FROM participants')->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Liste des participants</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Grade</th>
        <th>Action</th>
    </tr>
    <?php foreach ($participants as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['nom']) ?></td>
        <td><?= htmlspecialchars($p['prenom']) ?></td>
        <td><?= htmlspecialchars($p['grade']) ?></td>
        <td>
            <a href="?page=liste_participants&delete_id=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce participant ?');">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
