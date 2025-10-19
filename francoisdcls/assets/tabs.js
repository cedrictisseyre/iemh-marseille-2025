// Progressive enhancement: attach AJAX loader to nav tabs (<nav class="tabs"> links)
(function(){
  'use strict';
  function onReady(fn){ if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn); else fn(); }
  onReady(()=>{
    const nav = document.querySelector('nav.tabs');
    if (!nav) return;
    const links = Array.from(nav.querySelectorAll('a'));
    const panel = document.getElementById('main-content');
    if (!panel) return;

    // enhance each link: intercept click to fetch content and inject it
    links.forEach(a=>{
      // only enhance internal links
      const href = a.getAttribute('href') || '';
      if (!href || href.startsWith('http') ) return;
      a.addEventListener('click', (e)=>{
        e.preventDefault();
        // show loading
        const old = panel.innerHTML;
        panel.innerHTML = '<p>Chargement…</p>';
        fetch(href, {credentials:'same-origin'})
          .then(r=>{
            if (!r.ok) throw new Error('HTTP '+r.status);
            return r.text();
          })
          .then(html=>{
              const parser = new DOMParser();
              const doc = parser.parseFromString(html, 'text/html');
              // prefer explicit #main-content but fall back to body
              const frag = doc.getElementById('main-content') || doc.body;
              // sanitize using global helper if available (removes header/nav duplicates)
              if (window.__francois_sanitize_html && typeof window.__francois_sanitize_html === 'function') {
                panel.innerHTML = window.__francois_sanitize_html(html);
              } else if (frag) {
                panel.innerHTML = frag.innerHTML;
              } else {
                panel.innerHTML = html;
              }
            // mark active link
            links.forEach(l=>l.classList.remove('active'));
            a.classList.add('active');
            // update hash for back/forward support
            try { history.pushState({}, '', a.getAttribute('href')); } catch(e){}
          })
          .catch(err=>{
            console.error(err);
            panel.innerHTML = old; // restore
            alert('Erreur lors du chargement de la page');
          });
      });
    });

    // handle popstate to allow back/forward navigation
    window.addEventListener('popstate', ()=>{
      const path = location.pathname + location.search + location.hash;
      const target = links.find(l=> l.getAttribute('href') === path || l.getAttribute('href') === location.hash.replace(/^#/, ''));
      if (target) target.click();
    });
  });
})();
// End of tabs module. The previous home-specific tab-builder was removed to avoid
// duplicated tab UIs. We keep progressive enhancement that attaches to existing
// <nav class="tabs"> links above. When fetching pages we sanitize the fragment
// to remove any duplicated <header> or <nav> that server-rendered pages may include.

// Small enhancement: sanitize injected fragment by removing header/nav elements
function _sanitizeFragment(node){
  if (!node) return '';
  // remove header and nav elements inside the fragment to avoid duplicates
  const headers = node.querySelectorAll('header, nav');
  headers.forEach(h => h.remove());
  return node.innerHTML;
}

// Patch the fetch handling in the progressive enhancement above by monkey-patching
// the module's behaviour: replace Element.prototype._injectFromDoc for clarity.
// Instead of modifying the above closure, enhance the fetch-to-insert step by
// intercepting fetch responses via a small helper used in the click handler.

// Note: the main progressive enhancement IIFE already reads frag = doc.getElementById('main-content')
// then uses frag.innerHTML or the whole document; we cannot directly change that closure here
// without duplicating it. To keep changes minimal we redefine fetch replacement globally
// by providing a utility used by other parts of the app if needed.
window.__francois_sanitize_html = function(html){
  try{
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    // prefer an explicit #main-content
    let node = doc.getElementById('main-content');
    if (!node) {
      // try common wrappers
      node = doc.querySelector('main') || doc.querySelector('.container') || doc.body;
    }
    return _sanitizeFragment(node);
  } catch(e){
    return html;
  }
};
