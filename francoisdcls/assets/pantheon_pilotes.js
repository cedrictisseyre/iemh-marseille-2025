document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('pantheon-pilotes');
  if (!container) return;
  container.innerHTML = '<em>Chargement...</em>';
  fetch('../services/pantheon_pilotes.php')
    .then(r => r.json())
    .then(data => {
      if (!data.length) {
        container.innerHTML = '<p style="text-align:center;">Aucun champion trouvé.</p>';
        return;
      }
      let tabs = '<div class="tabs">' + data.map((c, i) => `<div class="tab${i===0?' active':''}" data-tab="tab${i}">${c.prenom} ${c.nom}</div>`).join('') + '</div>';
      let profiles = data.map((c, i) => `
        <div class="profile${i===0?' active':''}" id="tab${i}">
          <div class="info">
            ${c.photo ? `<img src="${c.photo}" alt="Photo de ${c.prenom} ${c.nom}">` : ''}
            <p><span class="label">Nom :</span> ${c.nom}</p>
            <p><span class="label">Prénom :</span> ${c.prenom}</p>
            <p><span class="label">Nombre de victoires :</span> ${c.nb_victoires}</p>
            <p><span class="label">Nombre de participations :</span> ${c.nb_participations}</p>
            <p><span class="label">Années de participations :</span> ${c.annees_participations.join(', ')}</p>
            <p><span class="label">Années de victoires :</span> ${c.annees_victoires.join(', ')}</p>
          </div>
        </div>
      `).join('');
      container.innerHTML = tabs + profiles;
      // Tabs JS
      const tabEls = container.querySelectorAll('.tab');
      const profileEls = container.querySelectorAll('.profile');
      tabEls.forEach((tab, i) => {
        tab.addEventListener('click', () => {
          tabEls.forEach(t => t.classList.remove('active'));
          profileEls.forEach(p => p.classList.remove('active'));
          tab.classList.add('active');
          profileEls[i].classList.add('active');
        });
      });
    });
});
