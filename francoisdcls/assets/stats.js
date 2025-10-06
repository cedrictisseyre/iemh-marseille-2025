document.addEventListener('DOMContentLoaded', function() {
  fetch('../services/stats_globales.php')
    .then(r => r.json())
    .then(data => {
      const statsDiv = document.getElementById('stats-globales');
      if (!statsDiv) return;
      statsDiv.innerHTML = `
        <h2>Statistiques globales</h2>
        <ul>
          <li><b>Pilotes :</b> ${data.nb_pilotes}</li>
          <li><b>Écuries :</b> ${data.nb_ecuries}</li>
          <li><b>Championnats :</b> ${data.nb_championnats}</li>
          <li><b>Participations :</b> ${data.nb_participations}</li>
        </ul>
      `;
    });
});
