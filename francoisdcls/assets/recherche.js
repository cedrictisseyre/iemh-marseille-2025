document.addEventListener('DOMContentLoaded', function () {
    // Utility to escape text for insertion
    function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[c]; }); }

    // Generic search handler for inputs with optional result container
    function attachSearch(inputId, outputId) {
        const input = document.getElementById(inputId);
        const output = document.getElementById(outputId);
        if (!input || !output) return;

        let timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = input.value.trim();
            if (!q) {
                output.innerHTML = '';
                return;
            }
            timer = setTimeout(function () {
                output.innerHTML = '<div>Recherche...</div>';
                // Use secureFetch which injects CSRF header when available
                secureFetch('services/recherche_pilotes.php?q=' + encodeURIComponent(q))
                    .then(r => {
                        if (!r.ok) throw new Error('Network response not ok');
                        return r.json();
                    })
                    .then(data => {
                        if (!Array.isArray(data) || data.length === 0) {
                            output.innerHTML = '<div>Aucun pilote trouvé.</div>';
                            return;
                        }
                        const list = document.createElement('div');
                        list.setAttribute('role', 'listbox');
                        list.className = 'suggestions';
                        data.forEach(p => {
                            const item = document.createElement('div');
                            item.setAttribute('role', 'option');
                            item.className = 'suggestion-item';
                            item.tabIndex = 0;
                            item.dataset.id = p.pilote_id;
                            item.innerHTML = esc(p.prenom) + ' ' + esc(p.nom);
                            item.addEventListener('click', function () {
                                window.location.href = 'pages/fiche_pilote.php?id=' + encodeURIComponent(p.pilote_id);
                            });
                            item.addEventListener('keydown', function (ev) {
                                if (ev.key === 'Enter') item.click();
                            });
                            list.appendChild(item);
                        });
                        output.innerHTML = '';
                        output.appendChild(list);
                    })
                    .catch(err => {
                        output.innerHTML = '<div>Erreur lors de la recherche.</div>';
                        console.error(err);
                    });
            }, 250);
        });
    }

    // Attach to the dedicated search page
    attachSearch('input-recherche', 'resultats-recherche');
    // Attach to the homepage quick search
    attachSearch('input-recherche-home', 'suggestions-list');
});
