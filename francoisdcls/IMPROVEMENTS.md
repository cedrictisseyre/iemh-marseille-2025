## Améliorations moyennes / avancées — projet F1 (francoisdcls)

Ce document priorise des améliorations non-triviales mais peu risquées pour améliorer la sécurité, la maintenabilité, la qualité et l'accessibilité du site.

1) Protection CSRF (priorité: haute)
  - Pourquoi: Toutes les routes POST (ajout/modif/suppression) sont vulnérables aux requêtes CSRF. Protéger empêche les actions initiées depuis d'autres sites.
  - Approche: ajouter un helper `includes/csrf.php` qui expose `csrf_token()` et `csrf_field()` et vérifie le token dans les handlers POST.
  - Fichiers impactés: `services/*.php`, `pages/*_form.php`, `includes/header.php` (si présent)
  - Exemple minimal (à implémenter):
    - Générer token: `$_SESSION['_csrf']=bin2hex(random_bytes(24))`
    - Insérer dans formulaires: `<input type="hidden" name="_csrf" value="<?= csrf_token() ?>">`
    - Vérifier: `hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf'] ?? '')`

2) Déplacer `assets/photos_cache/` hors du dépôt + `.gitignore` (priorité: haute)
  - Pourquoi: Le cache d'images alourdit le dépôt et contient des blobs binaires. Il doit être généré, pas versionné.
  - Action: ajouter `assets/photos_cache/` à la racine `.gitignore` et déplacer les fichiers existants en stockage temporaire si besoin.
  - Fichiers impactés: `.gitignore`, documentation `README.md` ou `IMPROVEMENTS.md`

3) Validation serveur et affichage d'erreurs utilisateurs (priorité: moyenne)
  - Pourquoi: Les formulaires acceptent parfois des données invalides et lèvent des erreurs SQL. Valider côté serveur améliore la robustesse.
  - Approche: centraliser helpers de validation `includes/validation.php` et renvoyer des erreurs via `flash()` et pré-remplissage des formulaires.

4) Ajouter tests unitaires / tests d'intégration rapides (priorité: moyenne)
  - Pourquoi: Prévenir les régressions lors des refactors (forms/DB). Le projet est en PHP, on peut utiliser PHPUnit ou PestPHP.
  - Proposition: ajouter un job GitHub Actions `phpunit` qui lance un petit jeu de tests sur les helpers DB, validation et CSRF.
  - Fichiers: `tests/`, `composer.json` (pour phpunit), `.github/workflows/phpunit.yml`.

5) CSRF + token et sessions sécurisées (suite) (priorité: moyenne)
  - Assurer que `session_start()` est appelé dans un include central (`includes/init.php`).
  - Utiliser les options `session_set_cookie_params(['samesite'=>'Lax','secure'=>true,'httponly'=>true])` si le site utilise HTTPS.

6) Amélioration des toasts / feedback UX (priorité: faible)
  - Uniformiser l'affichage des messages (success/error) et ajouter une fermeture automatique + accessibilité (role="status").

7) Endpoint d'édition/suppression d'écurie (priorité: faible)
  - Pourquoi: On a edit/delete pour `pilotes`, faire de même pour `ecuries` pour cohérence.
  - Fichiers à ajouter: `pages/edit_ecurie.php`, `services/modifier_ecurie.php`, `services/supprimer_ecurie.php`.

8) Sécurité SQL & erreurs (priorité: moyenne)
  - Vérifier que toutes les requêtes utilisent des requêtes préparées (PDO) avec typage si possible.
  - Log des erreurs SQL sur fichier (log) pour débogage serveur (ne pas exposer au client).

9) Accessibility improvements deeper (priorité: faible)
  - Ajouter aria-labels sur les boutons d'action, s'assurer que les images ont alt descriptif, vérifier contraste couleurs (outil axe).

Plan d'implémentation rapide (sprint de 1 jour estimé):
- Matin (3h): CSRF helper + intégrer sur les handlers POST critiques (ajout_pilote, modifier_pilote, supprimer_pilote, ajout_ecurie,...).
- Après-midi (3h): `.gitignore` + nettoyage `assets/photos_cache/`, documentation et commit; ajouter `session` init central.
- Fin de journée (2h): tests unitaires de base + workflow GitHub Actions minimal.

Extraits de code utiles

1) `includes/csrf.php` (proposition):

```php
<?php
session_start();
function csrf_token(): string {
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(24));
  }
  return $_SESSION['_csrf'];
}
function csrf_field(): string {
  return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token()) . '">';
}
function csrf_check_or_die(): void {
  if (!hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf'] ?? '')) {
    http_response_code(403);
    die('CSRF validation failed');
  }
}
```

2) `.gitignore` ajout (ligne à ajouter):

```
# Cache d'images générées
francoisdcls/assets/photos_cache/
```

3) Exemple d'intégration dans un handler `services/supprimer_pilote.php`:

```php
require_once __DIR__ . '/../includes/csrf.php';
csrf_check_or_die();
// ensuite logique existante de suppression
```

---

Si vous validez ce plan, je peux commencer par:
- implémenter `includes/csrf.php` et ajouter l'appel de vérification dans les handlers POST critiques (3-5 fichiers), puis
- ajouter l'entrée `.gitignore` et déplacer `assets/photos_cache/` hors du suivi git (ou créer un commit pour suppression suivie d'une règle .gitignore).

Indiquez si vous souhaitez que je commence par le CSRF ou par le nettoyage du cache d'images en premier. 
