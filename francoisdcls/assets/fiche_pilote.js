document.addEventListener('DOMContentLoaded', function() {
  const ficheDiv = document.getElementById('fiche-pilote-dyn');
  if (!ficheDiv) return;
  const id = ficheDiv.dataset.id;
  if (!id) return;
  ficheDiv.innerHTML = '<em>Chargement...</em>';
  fetch('../services/fiche_pilote.php?id=' + encodeURIComponent(id))
    .then(r => r.json())
    .then(data => {
      if (data.error) {
        ficheDiv.innerHTML = '<p style="color:#b00">' + data.error + '</p>';
        return;
      }
      ficheDiv.innerHTML = `
        <ul>
          <li><b>Nom :</b> ${data.prenom} ${data.nom}</li>
          <li><b>Nationalité :</b> ${data.nationalite || 'N/A'}</li>
          <li><b>Nombre de titres :</b> ${data.nb_titres}</li>
          <li><b>Nombre de participations :</b> ${data.nb_participations}</li>
          <li><b>Écuries :</b> ${(data.ecuries && data.ecuries.length) ? data.ecuries.join(', ') : 'N/A'}</li>
        </ul>
      `;
    });
});
