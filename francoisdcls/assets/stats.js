(function () {
    'use strict';

    function number(v) {
        return (typeof v === 'number') ? v.toLocaleString() : (v || '0');
    }

    function renderStats(data) {
        var container = document.getElementById('stats-globales');
        if (!container) return;
        // Accessible heading id referenced from the region
        var titleId = 'stats-title';
        var h = document.createElement('h2');
        h.id = titleId;
        h.textContent = 'Statistiques globales';

        var ul = document.createElement('ul');
        ul.className = 'stats-list';

        var items = [
            ['Pilotes', data.nb_pilotes],
            ['Écuries', data.nb_ecuries],
            ['Championnats', data.nb_championnats],
            ['Participations', data.nb_participations]
        ];

        items.forEach(function (it) {
            var li = document.createElement('li');
            li.innerHTML = '<strong>' + it[0] + ':</strong> ' + number(Number(it[1]));
            ul.appendChild(li);
        });

        // Clear and append
        container.innerHTML = '';
        container.appendChild(h);
        container.appendChild(ul);
        // Link region to heading for screen readers
        container.setAttribute('aria-labelledby', titleId);
    }

    function renderError(msg) {
        var container = document.getElementById('stats-globales');
        if (!container) return;
        container.innerHTML = '';
        var err = document.createElement('div');
        err.className = 'error';
        err.setAttribute('role', 'alert');
        err.textContent = msg || 'Impossible de charger les statistiques.';
        container.appendChild(err);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var base = (typeof window !== 'undefined' && window.BASE_PATH) ? window.BASE_PATH : '';
        var url = base + 'services/stats_globales.php';

        // If siteFunctions.fetchJson exists, prefer it (uniform error handling)
        if (window.siteFunctions && typeof window.siteFunctions.fetchJson === 'function') {
            window.siteFunctions.fetchJson(url)
                .then(function (data) {
                    if (!data || data.error) {
                        renderError(data && data.error ? data.error : 'Données invalides');
                        return;
                    }
                    renderStats(data);
                })
                .catch(function (err) {
                    console.error('stats fetch failed', err);
                    renderError();
                });
            return;
        }

        // Fallback to plain fetch
        fetch(url, { credentials: 'same-origin' })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                if (!data || data.error) {
                    renderError(data && data.error ? data.error : 'Données invalides');
                    return;
                }
                renderStats(data);
            })
            .catch(function (err) {
                console.error('stats fetch failed', err);
                renderError();
            });
    });
})();
