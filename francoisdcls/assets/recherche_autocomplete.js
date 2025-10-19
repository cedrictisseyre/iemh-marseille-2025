// Autocomplete for the home search box
(function () {
    'use strict';
    function el(id)
    {
        return document.getElementById(id); }
    var input = el('input-recherche-home');
    var suggestions = el('suggestions-list');
    var typeSelect = el('select-recherche-home-type');
    var yearInput = el('input-recherche-home-annee');

    if (!input || !suggestions) {
        return;
    }

    var timer = null;
    var items = [];
    var activeIndex = -1;

    function clearSuggestions()
    {
        suggestions.innerHTML = '';
        suggestions.removeAttribute('aria-activedescendant');
        items = [];
        activeIndex = -1;
    }

    function renderEmpty()
    {
        clearSuggestions();
        var d = document.createElement('div');
        d.className = 'suggest-empty';
        d.textContent = 'Aucun résultat';
        suggestions.appendChild(d);
    }

    function setActive(i)
    {
        if (i < 0 || i >= items.length) {
            return;
        }
        if (activeIndex >= 0 && items[activeIndex]) {
            items[activeIndex].classList.remove('active');
        }
        activeIndex = i;
        var node = items[activeIndex];
        node.classList.add('active');
        suggestions.setAttribute('aria-activedescendant', node.id || '');
        node.scrollIntoView({ block: 'nearest' });
    }

    function chooseItem(i)
    {
        if (i < 0 || i >= items.length) {
            return;
        }
        var meta = items[i].dataset;
        if (meta.type === 'pilote' && meta.piloteId) {
            window.location.href = window.BASE_PATH + '/pages/fiche_pilote.php?id=' + meta.piloteId;
        } else if (meta.type === 'ecurie' && meta.ecurieId) {
            window.location.href = window.BASE_PATH + '/pages/fiche_ecurie.php?id=' + meta.ecurieId;
        }
    }

    input.addEventListener('keydown', function (ev) {
        if (!suggestions) {
            return;
        }
        if (ev.key === 'ArrowDown') {
            ev.preventDefault();
            if (items.length === 0) {
                return;
            }
            setActive((activeIndex + 1) % items.length);
        } else if (ev.key === 'ArrowUp') {
            ev.preventDefault();
            if (items.length === 0) {
                return;
            }
            setActive((activeIndex - 1 + items.length) % items.length);
        } else if (ev.key === 'Enter') {
            if (activeIndex >= 0) {
                ev.preventDefault();
                chooseItem(activeIndex);
            }
        } else if (ev.key === 'Escape') {
            clearSuggestions();
        }
    });

    input.addEventListener('input', function () {
        var q = input.value.trim();
        if (timer) {
            clearTimeout(timer);
        }
        if (q.length < 2) {
            clearSuggestions(); return; }
      // limit query length to avoid abuse
        if (q.length > 80) {
            q = q.substr(0, 80);
        }
        timer = setTimeout(function () {
            var params = new URLSearchParams();
            params.set('q', q);
            params.set('type', typeSelect ? typeSelect.value : 'both');
            if (yearInput && yearInput.value) {
                params.set('annee', yearInput.value);
            }
            var url = (typeof window.BASE_PATH !== 'undefined' ? window.BASE_PATH : '') + '/services/recherche_pilotes.php?' + params.toString();
            (window.siteFunctions && typeof window.siteFunctions.fetchJson === 'function' ? window.siteFunctions.fetchJson(url) : fetch(url).then(function (r) {
                return r.json(); }))
            .then(function (data) {
                clearSuggestions();
                if (!Array.isArray(data) || data.length === 0) {
                    renderEmpty(); return; }
                data.forEach(function (item, idx) {
                    var div = document.createElement('div');
                    div.className = 'suggest-item';
                    div.id = 'suggest-item-' + idx + '-' + Math.random().toString(36).slice(2,8);
                    div.setAttribute('role', 'option');
                  // store metadata on dataset for keyboard choice
                    if (item.type === 'pilote') {
                        div.textContent = (item.prenom ? item.prenom + ' ' : '') + (item.nom || '');
                        div.dataset.type = 'pilote';
                        div.dataset.piloteId = item.pilote_id || '';
                        div.addEventListener('click', function () {
                            window.location.href = window.BASE_PATH + '/pages/fiche_pilote.php?id=' + item.pilote_id; });
                    } else if (item.type === 'ecurie') {
                        div.textContent = item.ecurie_nom || 'Écurie';
                        div.dataset.type = 'ecurie';
                        div.dataset.ecurieId = item.ecurie_id || '';
                        div.addEventListener('click', function () {
                            window.location.href = window.BASE_PATH + '/pages/fiche_ecurie.php?id=' + item.ecurie_id; });
                    }
                    suggestions.appendChild(div);
                    items.push(div);
                });
              // set first active for keyboard friendliness
                if (items.length) {
                    setActive(0);
                }
            })
            .catch(function (err) {
                console.error('Autocomplete fetch failed', err); renderEmpty(); });
        }, 180);
    });
})();
