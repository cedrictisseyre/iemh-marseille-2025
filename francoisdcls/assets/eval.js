document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('eval-score');
    if (!el) {
        return;
    }
    fetch('/francoisdcls/pages/evaluation.php')
    .then(r => r.text())
    .then(html => {
      // quick parse: find the <strong id="score">NUM</strong>
        const m = html.match(/<strong id="score">(\d+)<\/strong>/);
        if (m) {
            el.textContent = m[1];
        }
    }).catch(() => { el.textContent = 'N/A'; });
});
