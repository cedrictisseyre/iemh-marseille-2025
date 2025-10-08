<?php
require_once 'connexion.php';
include 'header.php';

$coachs = $conn->query("SELECT * FROM coachs ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center my-4">
    <h1>👨‍🏫 Liste des Coachs</h1>
</div>

<table class="table table-striped text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Prénom</th>
            <th>Spécialité</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($coachs as $coach): ?>
            <tr>
                <td><?= $coach['id'] ?></td>
                <td><?= htmlspecialchars($coach['prenom']) ?></td>
                <td><?= htmlspecialchars($coach['specialite']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
