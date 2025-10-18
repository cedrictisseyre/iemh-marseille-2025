// Small helper to refresh the evaluation badge by reading window.EVAL_SCORE if present
document.addEventListener('DOMContentLoaded', function () {
  var el = document.getElementById('eval-score');
  if (!el) return;
  if (window.EVAL_SCORE) {
    el.textContent = String(window.EVAL_SCORE);
  } else {
    // fallback: request evaluation endpoint if present
    if (window.fetch) {
      var base = (window.BASE_PATH || '');
      fetch(base + '/pages/evaluation.php').then(function (r) {
        return r.text();
      }).then(function (t) {
        // try to parse a numeric score in the response
        var m = t.match(/Score:\s*(\d{1,3})/);
        if (m) el.textContent = m[1];
      }).catch(function () {/* ignore */});
    }
  }
});
