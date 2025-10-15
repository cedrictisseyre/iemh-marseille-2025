## Contribuer et conventions du projet

Merci de contribuer ! Ce fichier décrit quelques règles simples pour garder le dépôt propre
et éviter la duplication de fichiers de configuration.

Règles principales
- Les fichiers de configuration canoniques sont dans `francoisdcls/config/`.
  - `francoisdcls/config/phpunit.xml`
  - `francoisdcls/config/phpcs.xml`
- N'ajoutez pas de copies de ces fichiers à la racine du dépôt. Si vous devez modifier
  la configuration, éditez les fichiers sous `francoisdcls/config/`.
- Evitez d'ajouter des artifacts (fichiers temporaires, cache PHPUnit) au dépôt.

Commandes utiles (depuis la racine du dépôt)

```bash
# Installer les dépendances
composer install

# Lancer PHPUnit (configuration canonique)
./vendor/bin/phpunit --configuration francoisdcls/config/phpunit.xml

# Lancer PHPCS (ruleset canonique)
./vendor/bin/phpcs --standard=francoisdcls/config/phpcs.xml francoisdcls/

# Vérifier la syntaxe d'un fichier
php -l francoisdcls/site_f1.php
```

Bonnes pratiques de commit
- Messages courts et explicites : `type(scope): description` (ex. `fix(pages): correctif accessibilite sur site_f1`)
- Grouper changements logiques dans un même commit, éviter les commits mixtes (UI + refactor lourd)

Merci — en cas de doute, créez une PR et décrivez votre changement dans la description.
