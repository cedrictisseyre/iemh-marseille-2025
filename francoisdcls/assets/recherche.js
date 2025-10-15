document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-recherche');
    const input = document.getElementById('input-recherche');
    const resultDiv = document.getElementById('resultats-recherche');
    if (!form || !input || !resultDiv) {
        return;
    }
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const q = input.value.trim();
        if (!q) {
            return;
        }
        resultDiv.innerHTML = '<em>Recherche...</em>';
        fetch('../services/recherche_pilotes.php?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                resultDiv.innerHTML = '<p>Aucun pilote trouvé.</p>';
            } else {
                resultDiv.innerHTML = '<ul>' + data.map(p => ` < li > < a href = 'fiche_pilote.php?id=${p.pilote_id}' > ${p.prenom} ${p.nom} < / a > < / li > `).join('') + '</ul>';
            }
        });
    });
});
