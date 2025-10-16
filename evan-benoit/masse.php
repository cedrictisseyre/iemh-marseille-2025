<?php
include 'header.html';
require 'connexion.php';

// Récupérer tous les clients
$clients = $conn->query("SELECT * FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section bg-white">
  <div class="container">
    <h2>⚖️ Suivi de Masse Corporelle</h2>
    <p class="text-center mb-5">
      Visualise l’évolution du poids de chaque client pour suivre la progression et ajuster les programmes d’entraînement.
    </p>

    <?php foreach ($clients as $client): ?>
      <?php
        // Récupérer les masses de ce client
        $stmt = $conn->prepare("SELECT id, date_mesure, masse FROM suivi_masse WHERE id_client = ? ORDER BY date_mesure ASC");
        $stmt->execute([$client['id']]);
        $masses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dates = [];
        $valeurs = [];
        foreach ($masses as $m) {
          $dates[] = $m['date_mesure'];
          $valeurs[] = $m['masse'];
        }
      ?>

      <div class="card card-custom p-4 mb-5 shadow-sm">
        <h4 class="mb-3 text-primary">
          <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
        </h4>

        <?php if (count($masses) > 0): ?>
          <!-- Tableau des mesures -->
          <div class="table-responsive mb-4">
            <table class="table table-striped align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th>Date</th>
                  <th>Masse (kg)</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($masses as $m): ?>
                  <tr>
                    <td><?= htmlspecialchars($m['date_mesure']) ?></td>
                    <td><?= htmlspecialchars($m['masse']) ?></td>
                    <td>
                      <a href="modifier_masse.php?id=<?= $m['id'] ?>" class="btn btn-warning btn-sm">✏️ Modifier</a>
                      <a href="supprimer_masse.php?id=<?= $m['id'] ?>" class="btn btn-danger btn-sm"
                         onclick="return confirm('Supprimer cette mesure ?');">🗑 Supprimer</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Graphique -->
          <canvas id="graph-<?= $client['id'] ?>" height="100"></canvas>
          <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
          <script>
            const ctx<?= $client['id'] ?> = document.getElementById('graph-<?= $client['id'] ?>').getContext('2d');
            new Chart(ctx<?= $client['id'] ?>, {
              type: 'line',
              data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                  label: 'Masse (kg)',
                  data: <?= json_encode($valeurs) ?>,
                  borderColor: '#ff4b2b',
                  borderWidth: 3,
                  fill: false,
                  tension: 0.3,
                  pointRadius: 5,
                  pointHoverRadius: 7
                }]
              },
              options: {
                plugins: { legend: { display: false } },
                scales: {
                  y: { title: { display: true, text: 'Poids (kg)' }, beginAtZero: false },
                  x: { title: { display: true, text: 'Date' } }
                }
              }
            });
          </script>
        <?php else: ?>
          <p class="text-muted fst-italic">Aucune donnée enregistrée pour ce client.</p>
        <?php endif; ?>

        <div class="text-center mt-3">
          <a href="ajouter_masse.php?id_client=<?= $client['id'] ?>" class="btn btn-primary px-4">➕ Ajouter une mesure</a>
        </div>
      </div>

    <?php endforeach; ?>

    <div class="text-center mt-5">
      <a href="index.php" class="btn btn-outline-dark px-4 py-2">⬅️ Retour à l'accueil</a>
    </div>
  </div>
</section>

<?php include 'footer.html'; ?>
