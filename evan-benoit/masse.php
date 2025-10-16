<?php include 'header.html'; ?>

<section class="section bg-white">
  <div class="container">
    <h2>⚖️ Suivi de Masse Corporelle</h2>
    <p class="text-center mb-5">
      Visualise l'évolution du poids de chaque client pour suivre la progression et ajuster les programmes.
    </p>

    <div class="card card-custom p-4 mb-5">
      <h4 class="mb-4">Graphique de progression</h4>

      <canvas id="graphMasse" height="100"></canvas>

      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        const ctx = document.getElementById('graphMasse').getContext('2d');
        const graphMasse = new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4', 'Semaine 5'],
            datasets: [
              {
                label: 'Lucas Dupont',
                data: [72.5, 73.1, 73.8, 74.2, 74.6],
                borderColor: '#ff4b2b',
                borderWidth: 3,
                fill: false,
                tension: 0.3,
              },
              {
                label: 'Emma Martin',
                data: [61.3, 60.9, 60.5, 60.2, 60.0],
                borderColor: '#ff416c',
                borderWidth: 3,
                fill: false,
                tension: 0.3,
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'bottom' },
            },
            scales: {
              y: {
                title: { display: true, text: 'Poids (kg)' },
                beginAtZero: false
              },
              x: {
                title: { display: true, text: 'Semaine' }
              }
            }
          }
        });
      </script>
    </div>

    <div class="text-center">
      <button class="btn btn-primary px-4 py-2">📊 Ajouter une nouvelle mesure</button>
    </div>
  </div>
</section>

<?php include 'footer.html'; ?>
