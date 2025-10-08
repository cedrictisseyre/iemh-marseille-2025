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
        <div class="pantheon-card" style="max-width:340px;margin:2em auto;">
          <div class="pantheon-photo">
            ${data.photo ? `<img src="${data.photo}" alt="Photo de ${data.prenom} ${data.nom}">` : `<div class='no-photo'>?</div>`}
          </div>
          <div class="pantheon-info">
            <h3>${data.prenom} <span class="pantheon-nom">${data.nom}</span></h3>
            <div class="pantheon-titres"><span class="nb">${data.nb_titres}</span> titre${data.nb_titres>1?'s':''}</div>
            <div class="pantheon-annees"><span class="label">Nationalité :</span> ${data.nationalite || 'N/A'}</div>
            <div class="pantheon-participations"><span class="label">Participations :</span> ${data.nb_participations}</div>
            <div class="pantheon-annees"><span class="label">Écuries :</span> ${(data.ecuries && data.ecuries.length) ? data.ecuries.join(', ') : 'N/A'}</div>
          </div>
        </div>
      `;
    });
});
