// Helper minimal pour inclure automatiquement le token CSRF dans les requêtes fetch
// Usage: secureFetch(url, { method: 'POST', body: formData })

function secureFetch(url, options = {}) {
  options.headers = options.headers || {};
  if (typeof window !== 'undefined' && window.CSRF_TOKEN) {
    options.headers['X-CSRF-Token'] = window.CSRF_TOKEN;
  }
  return fetch(url, options);
}

// Expose global
window.secureFetch = secureFetch;
