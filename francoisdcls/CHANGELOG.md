```markdown
# Changelog

Toutes les modifications notables sont listées ici pour faciliter la revue et l'évaluation.

## 2025-10-15 — Réorganisation et hardening
- Consolidation des configurations de développement sous `francoisdcls/config/` :
  - `phpunit.xml`, `phpcs.xml` déplacés / canonisés
- Nettoyage de la racine : suppression des doublons (anciennes copies de phpunit/phpcs/tests)
- Ajout de scripts et fichiers d'aide dans `francoisdcls/` : `README.md`, `CONTRIBUTING.md`, `IMPROVEMENTS.md`, `schema.sql`, `seed.sql`
- Ajout et configuration d'un workflow CI (GitHub Actions) pointant vers les configs canoniques
- Restreindre l'exécution de PHPCS et PHPUnit aux changements affectant `francoisdcls/` via `dorny/paths-filter`
- Ajout d'un test PHPUnit basique et exécution locale vérifiée (OK 1 test, 5 assertions)
- Suppression des images de cache suivies (`assets/photos_cache` ignoré)

## Notes
- Les fichiers légitimes à la racine restent : `composer.json`, `composer.lock`, `.gitignore`.
- Recommandation : garder les configs canoniques sous `francoisdcls/config/` et ne pas recréer des copies à la racine.

```
