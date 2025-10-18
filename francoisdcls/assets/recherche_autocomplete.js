// Autocomplete for the home search box
(function () {
  function el(id) { return document.getElementById(id); }
  var input = el('input-recherche-home');
  var suggestions = el('suggestions-list');
  var typeSelect = el('select-recherche-home-type');
  var yearInput = el('input-recherche-home-annee');

  if (!input || !suggestions) return;

  var timer = null;
  input.addEventListener('input', function () {
    var q = input.value.trim();
    if (timer) clearTimeout(timer);
    if (q.length < 2) { suggestions.innerHTML = ''; return; }
    timer = setTimeout(function () {
      var params = new URLSearchParams();
      params.set('q', q);
      params.set('type', typeSelect ? typeSelect.value : 'both');
      if (yearInput && yearInput.value) params.set('annee', yearInput.value);
      var url = window.BASE_PATH + '/services/recherche_pilotes.php?' + params.toString();
      window.siteFunctions.fetchJson(url).then(function (data) {
        suggestions.innerHTML = '';
        if (!Array.isArray(data) || data.length === 0) {
          suggestions.innerHTML = '<div class="suggest-empty">Aucun résultat</div>';
          return;
        }
        data.forEach(function (item) {
          var div = document.createElement('div');
          div.className = 'suggest-item';
          if (item.type === 'pilote') {
            div.textContent = (item.prenom ? item.prenom + ' ' : '') + (item.nom || '');
            div.setAttribute('role','option');
            div.addEventListener('click', function () {
              window.location.href = window.BASE_PATH + '/pages/fiche_pilote.php?id=' + item.pilote_id;
            });
          } else if (item.type === 'ecurie') {
            div.textContent = item.ecurie_nom || 'Écurie';
            div.addEventListener('click', function () {
              window.location.href = window.BASE_PATH + '/pages/fiche_ecurie.php?id=' + item.ecurie_id;
            });
          }
          suggestions.appendChild(div);
        });
      }).catch(function (err) { console.error(err); });
    }, 200);
  });
})();
