// Quelques fonctions utilitaires front-end pour améliorer l'interactivité
function showToast(msg, ms = 3000) {
  var d = document.createElement('div');
  d.className = 'site-toast';
  d.textContent = msg;
  d.style = 'position:fixed;right:16px;bottom:16px;background:#333;color:#fff;padding:8px 12px;border-radius:6px;z-index:9999;';
  document.body.appendChild(d);
  setTimeout(function(){ d.remove(); }, ms);
}

function fetchJson(url) {
  return fetch(url, {credentials: 'same-origin'}).then(function(r){
    if (!r.ok) throw new Error('Network error');
    return r.json();
  });
}

function initSiteFunctions() {
  // simple hook: show a toast when the page is ready
  document.addEventListener('DOMContentLoaded', function(){
    // noop by default; can be used by pages to announce readiness
  });
}

// expose
window.siteFunctions = { showToast: showToast, fetchJson: fetchJson, init: initSiteFunctions };

// auto-init
initSiteFunctions();
