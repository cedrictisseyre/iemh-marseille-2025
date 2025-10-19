// Progressive enhancement: attach AJAX loader to nav tabs (<nav class="tabs"> links)
(function () {
    'use strict';
    function onReady(fn)
    {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn); } else {
            fn();
            } }
    onReady(() => {
        const nav = document.querySelector('nav.tabs');
        if (!nav) {
            return;
        }
        const links = Array.from(nav.querySelectorAll('a'));
        const panel = document.getElementById('main-content');
        if (!panel) {
            return;
        }

      // Optional visible debug box (enable by adding ?tabs_debug=1 to the URL)
        const debugEnabled = location.search.indexOf('tabs_debug=1') !== -1;
        let debugBox = null;
        if (debugEnabled) {
            debugBox = document.createElement('div');
            debugBox.style = 'position:fixed;right:12px;bottom:12px;max-width:40vw;z-index:9999;background:#111;color:#fff;padding:10px;border-radius:6px;font-size:12px;line-height:1.2;opacity:0.95;';
            debugBox.id = 'tabs-debug-box';
            debugBox.textContent = 'tabs debug active';
            document.body.appendChild(debugBox);
        }

      // helper to compute a normalized absolute path+search+hash for comparison
        function normalizeHref(href)
        {
            try {
              // use BASE_PATH (set by header.php) as the application root when available
                const basePath = (window.BASE_PATH || '/').replace(/\/$/, '');
                const baseForUrl = location.origin + (basePath || '/') + '/';
                const u = new URL(href, baseForUrl);
                return u.pathname + u.search + u.hash;
            } catch (e) {
                return href;
            }
        }

      // enhance each link: intercept click to fetch content and inject it
        links.forEach(a => {
          // only enhance internal links
            const href = a.getAttribute('href') || '';
          // skip external/empty links
            if (!href || href.startsWith('http') || href.startsWith('mailto:')) {
                return;
            }

          // store normalized href on the element for faster lookup later
            a.dataset._absHref = normalizeHref(href);

          // click handler extracted so popstate can call it without pushing state twice
            const handleClick = async function (e, options = {}) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                const skipPush = options.skipPush === true;
              // show loading
                const old = panel.innerHTML;
                panel.innerHTML = '<p>Chargement…</p>';
          // build an absolute (root-relative) fetch URL using BASE_PATH to avoid
          // resolving relative to the current document directory (fixes clicks
          // when on /pages/* pages where relative hrefs would otherwise break)
                const base = (window.BASE_PATH || '').replace(/\/$/, '');
                const pathPart = href.startsWith('/') ? href : '/' + href;
                const fetchUrl = (base || '') + pathPart;
                try {
                  // debug: log what we are about to fetch (helps diagnose bad URLs)
                    try {
                        console.debug('tabs: fetching', { fetchUrl: fetchUrl, href: href, BASE_PATH: window.BASE_PATH }); } catch (e) {
                        }
                        if (debugBox) {
                            debugBox.textContent = 'fetching: ' + fetchUrl + '\nhref: ' + href + '\nBASE_PATH: ' + (window.BASE_PATH || '');
                        }
                        const r = await fetch(fetchUrl, {credentials:'same-origin'});
                        if (!r.ok) {
                            const body = await r.text().catch(() => '<no body>');
                            console.error('Failed to fetch', fetchUrl, 'status=', r.status, 'body=', body);
                            if (debugBox) {
                                debugBox.textContent = 'failed: ' + r.status + '\n' + body.slice(0,2000);
                            }
                            panel.innerHTML = '<div class="fetch-error">Erreur ' + r.status + ' lors du chargement de la page. <pre style="white-space:pre-wrap">' +
                            escapeHtml(body).slice(0,2000) + '</pre></div>';
                          // fallback: navigate to the page so the user still sees it
                            try {
                                window.location.assign(fetchUrl); } catch (e) {
                                            /* ignore */ }
                                return;
                        }
                        const html = await r.text();
                        if (debugBox) {
                            debugBox.textContent = 'ok: ' + fetchUrl + '\nlen=' + html.length;
                        }
                        if (window.__francois_sanitize_html && typeof window.__francois_sanitize_html === 'function') {
                            panel.innerHTML = window.__francois_sanitize_html(html);
                        } else {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const frag = doc.getElementById('main-content') || doc.body;
                            panel.innerHTML = frag ? frag.innerHTML : html;
                        }
              // mark active link
                        links.forEach(l => l.classList.remove('active'));
                        a.classList.add('active');
              // update history using normalized absolute path so popstate can match
                        if (!skipPush) {
                            try {
                                const newUrl = normalizeHref(href);
                                history.pushState({}, '', newUrl);
                            } catch (e) {
                            }
                        }
                } catch (err) {
                    console.error('Fetch error for', fetchUrl, err);
                    if (debugBox) {
                        debugBox.textContent = 'exception: ' + String(err);
                    }
              // restore previous content then fallback to full navigation
                    panel.innerHTML = old;
                    try {
                        window.location.assign(fetchUrl); } catch (e) {
                                    /* ignore */ }
                }
            };

      // keep a reference so popstate can call it without dispatching events
            a._handleClick = handleClick;
            a.addEventListener('click', handleClick);
        });

    // handle popstate to allow back/forward navigation
    window.addEventListener('popstate', () => {
        const path = location.pathname + location.search + location.hash;
        const target = links.find(l => l.dataset && l.dataset._absHref === path);
        if (target) {
          // call the stored handler directly and skip pushing state
            if (typeof target._handleClick === 'function') {
                try {
                    target._handleClick(null, { skipPush: true }); } catch (e) {
                                  console.error(e); }
            } else {
                target.click();
            }
        } else {
          // if no matching link, optionally fetch current path to update content
          // (useful when user lands directly on a non-root url)
          // we only auto-load if the path is under our site
            const rel = path.replace(/^\//, '');
            const maybeLink = links.find(l => l.getAttribute('href') === rel);
            if (maybeLink) {
                maybeLink.click();
            }
        }
      });
    });
})();
// End of tabs module. The previous home-specific tab-builder was removed to avoid
// duplicated tab UIs. We keep progressive enhancement that attaches to existing
// <nav class="tabs"> links above. When fetching pages we sanitize the fragment
// to remove any duplicated <header> or <nav> that server-rendered pages may include.

// Small enhancement: sanitize injected fragment by removing header/nav elements
function _sanitizeFragment(node)
{
    if (!node) {
        return '';
    }
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
window.__francois_sanitize_html = function (html) {
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
      // prefer an explicit #main-content
        let node = doc.getElementById('main-content');
        if (!node) {
          // try common wrappers
            node = doc.querySelector('main') || doc.querySelector('.container') || doc.body;
        }
        return _sanitizeFragment(node);
    } catch (e) {
        return html;
    }
};

// small helper to escape text for rendering inside HTML
function escapeHtml(s)
{
    return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
