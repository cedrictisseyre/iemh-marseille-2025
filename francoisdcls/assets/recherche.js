document.addEventListener('DOMContentLoaded', function () {
    // Utility to escape text for insertion
    function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[c]; }); }

    // Generic search handler for inputs with optional result container
    function attachSearch(inputId, outputId, opts = {}) {
        const input = document.getElementById(inputId);
        const output = document.getElementById(outputId);
        if (!input || !output) return;

        let timer = null;
        let activeIndex = -1;
        const items = [];
        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = input.value.trim();
            if (!q) {
                output.innerHTML = '';
                return;
            }
            timer = setTimeout(function () {
                output.innerHTML = '<div>Recherche...</div>';
                // Build query params
                const params = new URLSearchParams();
                params.set('q', q);
                if (opts.type) params.set('type', opts.type);
                if (opts.annee) params.set('annee', opts.annee);
                // Use secureFetch which injects CSRF header when available
                secureFetch('services/recherche_pilotes.php?' + params.toString())
                    .then(r => {
                        if (!r.ok) throw new Error('Network response not ok');
                        return r.json();
                    })
                    .then(data => {
                        if (!Array.isArray(data) || data.length === 0) {
                            output.innerHTML = '<div>Aucun pilote trouvé.</div>';
                            return;
                        }
                        // build list with keyboard navigation support
                        const list = document.createElement('div');
                        list.setAttribute('role', 'listbox');
                        list.className = 'suggestions';
                        output.innerHTML = '';
                        items.length = 0; activeIndex = -1;
                        data.forEach((p, idx) => {
                            const item = document.createElement('div');
                            item.setAttribute('role', 'option');
                            item.className = 'suggestion-item';
                            item.tabIndex = -1;
                            item.dataset.index = idx;
                            item.dataset.id = p.pilote_id ?? p.ecurie_id ?? '';
                            const label = p.type === 'ecurie' ? (p.ecurie_nom + ' (' + (p.ecurie_pays ?? '') + ')') : (p.prenom + ' ' + p.nom);
                            item.textContent = label;
                            item.addEventListener('click', function () {
                                if (p.type === 'ecurie') {
                                    window.location.href = 'pages/fiche_ecurie.php?id=' + encodeURIComponent(p.ecurie_id);
                                } else {
                                    window.location.href = 'pages/fiche_pilote.php?id=' + encodeURIComponent(p.pilote_id);
                                }
                            });
                            list.appendChild(item);
                            items.push(item);
                        });
                        output.appendChild(list);
                        // handle keyboard navigation on input
                        input.addEventListener('keydown', function (ev) {
                            if (ev.key === 'ArrowDown') {
                                ev.preventDefault();
                                activeIndex = Math.min(items.length - 1, activeIndex + 1);
                                updateActive();
                            } else if (ev.key === 'ArrowUp') {
                                ev.preventDefault();
                                activeIndex = Math.max(0, activeIndex - 1);
                                updateActive();
                            } else if (ev.key === 'Enter') {
                                if (activeIndex >= 0 && items[activeIndex]) {
                                    items[activeIndex].click();
                                    ev.preventDefault();
                                }
                            }
                        });
                        function updateActive() {
                            items.forEach((it, i) => {
                                if (i === activeIndex) {
                                    it.classList.add('active');
                                    it.tabIndex = 0;
                                    it.focus();
                                } else {
                                    it.classList.remove('active');
                                    it.tabIndex = -1;
                                }
                            });
                        }
                    })
                    .catch(err => {
                        output.innerHTML = '<div>Erreur lors de la recherche.</div>';
                        console.error(err);
                    });
            }, 250);
        });
    }

    // Attach to the dedicated search page (reads type and year from selectors)
    attachSearch('input-recherche', 'resultats-recherche', { typeSelector: 'select-recherche-type', anneeSelector: 'input-recherche-annee' });
    // Attach to the homepage quick search (reads type and year from homepage selectors)
    attachSearch('input-recherche-home', 'suggestions-list', { typeSelector: 'select-recherche-home-type', anneeSelector: 'input-recherche-home-annee' });
});
