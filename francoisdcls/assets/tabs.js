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
            const frag = doc.getElementById('main-content');
            if (frag) panel.innerHTML = frag.innerHTML; else panel.innerHTML = html;
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
// Tabs loader for site_f1
(function(){
  'use strict';

  const pages = [
    { href: 'pages/liste_pilotes.php', label: 'Liste des pilotes' },
    { href: 'pages/liste_ecuries.php', label: 'Liste des écuries' },
    { href: 'pages/statistiques.php', label: 'Statistiques' },
    { href: 'pages/recherche.php', label: 'Recherche de pilotes' },
    { href: 'pages/comparer_pilotes.php', label: 'Comparer deux pilotes' },
    { href: 'pages/palmares_annee.php', label: 'Palmarès par année' },
    { href: 'pages/pantheon_pilotes.php', label: 'Champions du monde' }
  ];

  function onReady(fn){
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
    else fn();
  }

  onReady(()=>{
    const path = window.location.pathname || '';
    // Only activate tabs on homepage (site_f1.php)
    if (!path.endsWith('/site_f1.php') && !path.endsWith('/')) return;

    const main = document.getElementById('main-content');
    if (!main) return;

    // Determine base prefix: if current path contains /francoisdcls/ then use that prefix
    const base = path.indexOf('/francoisdcls/') !== -1 ? 'francoisdcls/' : '';

    // Build tab UI
    const tabsWrap = document.createElement('div');
    tabsWrap.className = 'home-tabs';
    const tabList = document.createElement('ul');
    tabList.className = 'tabs-list';
    tabList.setAttribute('role','tablist');

  const panel = document.createElement('div');
  const panelId = 'home-tab-panel';
  panel.id = panelId;
  panel.setAttribute('role','tabpanel');
  panel.setAttribute('tabindex','0');
    panel.style.minHeight = '200px';
    panel.style.marginTop = '1em';

    const cache = new Map();

    pages.forEach((p, idx)=>{
      const li = document.createElement('li');
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'tab-button';
      btn.setAttribute('role','tab');
      btn.setAttribute('aria-selected','false');
  btn.dataset.url = base + p.href;
  btn.dataset.key = p.href.replace(/[^a-z0-9]/gi,'_');
  // Accessibility attributes
  btn.id = 'tab-' + btn.dataset.key;
  btn.setAttribute('aria-controls', panelId);
  // set initial tabindex (only first will be 0 below)
  btn.setAttribute('tabindex', '-1');
      btn.textContent = p.label;
      btn.addEventListener('click', ()=> activateTab(btn));
      btn.addEventListener('keydown', (e)=> handleKey(e, idx));
      li.appendChild(btn);
      tabList.appendChild(li);
    });

    tabsWrap.appendChild(tabList);
    // insert tabs at top of main
    main.insertBefore(tabsWrap, main.firstChild);
    main.insertBefore(panel, tabsWrap.nextSibling);

    // Activate default (either hash or first)
    const hash = window.location.hash && window.location.hash.substring(1);
    let defaultBtn = tabList.querySelector('button');
    if (hash) {
      const found = tabList.querySelector(`button[data-key='${hash}']`);
      if (found) defaultBtn = found;
    }
    if (defaultBtn) activateTab(defaultBtn, {pushState:false});

    function setActiveButton(btn){
      const buttons = Array.from(tabList.querySelectorAll('button'));
      buttons.forEach((b,i)=>{
        const selected = b===btn;
        b.setAttribute('aria-selected', selected ? 'true' : 'false');
        b.classList.toggle('active', selected);
        // Manage keyboard navigation focus order
        b.setAttribute('tabindex', selected ? '0' : '-1');
      });
      // aria-labelledby for panel
      if (btn && btn.id) panel.setAttribute('aria-labelledby', btn.id);
    }

    function handleKey(e, idx){
      const count = pages.length;
      if (e.key === 'ArrowRight' || e.key === 'ArrowLeft'){
        e.preventDefault();
        const next = e.key === 'ArrowRight' ? (idx+1)%count : (idx-1+count)%count;
        const btn = tabList.querySelectorAll('button')[next];
        btn.focus();
        activateTab(btn);
      } else if (e.key === 'Home') {
        e.preventDefault();
        const btn = tabList.querySelectorAll('button')[0]; btn.focus(); activateTab(btn);
      } else if (e.key === 'End') {
        e.preventDefault();
        const btns = tabList.querySelectorAll('button'); const btn = btns[btns.length-1]; btn.focus(); activateTab(btn);
      }
    }

    function activateTab(btn, opts={pushState:true}){
      setActiveButton(btn);
      const url = btn.dataset.url;
      const key = btn.dataset.key;
      if (cache.has(key)){
        panel.innerHTML = cache.get(key);
        return;
      }
      panel.innerHTML = '<p>Chargement...</p>';
      fetch(url, {credentials:'same-origin'})
        .then(r=>{
          if (!r.ok) throw new Error('Erreur HTTP '+r.status);
          return r.text();
        })
        .then(html=>{
          // parse and extract main-content
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const fragment = doc.getElementById('main-content');
          let content = '';
          if (fragment) content = fragment.innerHTML;
          else content = doc.body.innerHTML;
          cache.set(key, content);
          panel.innerHTML = content;
        })
        .catch(err=>{
          panel.innerHTML = '<div class="error">Impossible de charger le contenu.</div>';
          console.error(err);
        });
      if (opts.pushState) {
        const stateKey = btn.dataset.key;
        try { history.replaceState({}, '', '#'+stateKey); } catch(e){}
      }
    }
  });
})();
