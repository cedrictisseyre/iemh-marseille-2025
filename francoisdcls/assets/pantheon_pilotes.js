document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('pantheon-pilotes');
    if (!container) {
        return;
    }
    container.innerHTML = '<em>Chargement...</em>';
    fetch('../services/pantheon_pilotes.php')
    .then(r => r.json())
    .then(data => {
        if (!data.length) {
            container.innerHTML = '<p style="text-align:center;">Aucun champion trouvé.</p>';
            return;
        }
        let cards = '<div class="pantheon-grid">' + data.map((c) => `
        < div class = "pantheon-card" >
          < div class = "pantheon-photo" >
            ${c.photo ? ` < img src = "${c.photo}" alt = "Photo de ${c.prenom} ${c.nom}" > ` : ` < div class = 'no-photo' > ? < / div > `}
          <  / div >
          < div class = "pantheon-info" >
            < h3 > ${c.prenom} < span class = "pantheon-nom" > ${c.nom} < / span > < / h3 >
            < div class = "pantheon-titres" > < span class = "nb" > ${c.nb_victoires} < / span > titre${c.nb_victoires > 1 ? 's' : ''} < / div >
            < div class = "pantheon-annees" > < span class = "label" > Années : < / span > ${c.annees_victoires.join(', ')} < / div >
            < div class = "pantheon-participations" > < span class = "label" > Participations : < / span > ${c.nb_participations} (${c.annees_participations.join(', ')}) < / div >
          <  / div >
        <  / div >
        `).join('') + '</div>';
      container.innerHTML = cards;
    });
});
