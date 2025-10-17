# Site F1 - Fiches Pilotes et Statistiques

Ce projet propose un site web dynamique permettant de consulter des fiches d'informations sur les pilotes de Formule 1, leurs statistiques et d'autres données issues d'une base MySQL.

## Fonctionnalités principales
- Page d'accueil centralisée avec navigation
- Liste des pilotes et fiche détaillée (titres, participations, écuries)
- Liste des écuries et fiche écurie (pilotes associés)
- Statistiques globales et classements (top pilotes, top écuries)
- Recherche dynamique de pilotes (AJAX)
- Comparaison de deux pilotes (titres, participations, écuries)
- Palmarès par année (champion et participants)
- Panthéon des champions du monde (onglets dynamiques)
- Services API REST (JSON) pour toutes les entités et stats

## Prérequis
- PHP >= 8.0 avec extensions PDO et pdo_mysql
- MySQL (local ou distant) avec une base contenant les tables nécessaires (pilotes, championnats, participations, etc.)
- Serveur web (Apache, Nginx ou PHP built-in)

## Installation
1. Cloner ce dépôt
2. Configurer la connexion à la base dans `francoisdcls/database/bdd_formule1.php`
3. Importer le schéma et les données MySQL si besoin (fichiers `schema.sql` et `seed.sql` fournis dans ce dossier)
	- Exemple rapide (depuis la racine du projet) :

```sql
CREATE DATABASE f1_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE f1_demo;
SOURCE francoisdcls/schema.sql;
SOURCE francoisdcls/seed.sql;
```

4. Lancer le serveur web ou utiliser `php -S localhost:8000` dans le dossier du projet


## Organisation du projet

```
francoisdcls/
├── assets/         # Images, CSS, JS (style.css, logo-f1.svg, stats.js, recherche.js, fiche_pilote.js, pantheon_pilotes.js...)
├── database/       # Connexion et scripts SQL (bdd_formule1.php)
├── pages/          # Pages HTML/PHP (index.php, liste_pilotes.php, fiche_pilote.php, liste_ecuries.php, fiche_ecurie.php, statistiques.php, recherche.php, comparer_pilotes.php, palmares_annee.php, pantheon_pilotes.php)
├── services/       # Scripts PHP pour accès AJAX/API (pilotes.php, ecuries.php, championnats.php, participations.php, recherche_pilotes.php, stats_globales.php, fiche_pilote.php, pantheon_pilotes.php...)
```

## Utilisation
- Accéder à `pages/index.php` pour la navigation principale
- Utiliser les différents liens pour explorer pilotes, écuries, stats, palmarès, panthéon...
- Les services du dossier `services/` peuvent être consommés en AJAX ou par des applications externes (format JSON)

## Auteur
Projet IEMH Marseille 2025 - François Duclos

## Notes additionnelles
- `schema.sql` et `seed.sql` ont été ajoutés pour faciliter les tests locaux.
- Voir `IMPROVEMENTS.md` pour la liste des améliorations proposées (CSRF, tests, CI, nettoyage du cache d'images).

## Configs et commandes de développement

Les fichiers de configuration canoniques pour les outils de développement se trouvent sous `francoisdcls/config/` :

- `francoisdcls/config/phpunit.xml` - configuration PHPUnit (bootstrap relatif au répertoire racine du dépôt)
- `francoisdcls/config/phpcs.xml` - configuration PHP_CodeSniffer (ruleset PSR12)

Commandes utiles depuis la racine du dépôt :

```bash
# Lancer PHPUnit en utilisant la configuration canonique
./vendor/bin/phpunit --configuration francoisdcls/config/phpunit.xml

# Lancer PHPCS avec la ruleset du projet
./vendor/bin/phpcs --standard=francoisdcls/config/phpcs.xml francoisdcls/

# Vérifier la syntaxe PHP d'un fichier
php -l francoisdcls/site_f1.php
```

### Notes sur les fichiers de configuration

Les fichiers de configuration canoniques sont maintenant placés sous `francoisdcls/config/`.
Les copies précédentes à la racine de `francoisdcls/` ont été sauvegardées sous
`francoisdcls/config/backup/` et remplacées par de petits placeholders pour éviter
la confusion. Si vous avez des outils qui pointent encore vers les fichiers racine,
mettez-les à jour pour utiliser `francoisdcls/config/phpunit.xml` et
`francoisdcls/config/phpcs.xml`.

Backups conservés :

```
francoisdcls/config/backup/phpcs.xml
francoisdcls/config/backup/phpunit.xml
```

Si vous préférez supprimer les placeholders racine définitivement, dites-le et je
les retirerai.

## Tests & CI

Le dépôt contient un workflow GitHub Actions qui exécute des vérifications (php -l, phpcs et phpunit) pour les changements affectant uniquement le dossier `francoisdcls/`. Cela évite d'exécuter la CI pour d'autres travaux présents dans le dépôt.

Localement, pour lancer les vérifications :

```bash
# Vérifier la syntaxe PHP
find francoisdcls -name "*.php" -print0 | xargs -0 -n1 php -l

# Linter (PHPCS) — nécessite phpcs installé via composer
./vendor/bin/phpcs --standard=francoisdcls/config/phpcs.xml francoisdcls/

# Tests PHPUnit
./vendor/bin/phpunit --configuration francoisdcls/config/phpunit.xml
```

Remarque : les configurations à la racine ont été consolidées dans `francoisdcls/config/`. Utilisez ces chemins pour exécuter les tests et le linter localement ou dans votre CI.

## Forcer la base de données utilisée (MySQL vs SQLite)

Le projet peut utiliser soit une base MySQL (par défaut en production) soit un fichier SQLite local pour les tests.

- Pour forcer MySQL localement, créez un fichier `francoisdcls/.env` contenant :

```
FRANCOISDB_DRIVER=mysql
FRANCOISDB_HOST=127.0.0.1
FRANCOISDB_NAME=francois_duclos
FRANCOISDB_USER=root
FRANCOISDB_PASS=secret
```

- Pour forcer SQLite (utilisé par la suite de tests et le bootstrap PHPUnit), mettez :

```
FRANCOISDB_DRIVER=sqlite
```

- Vous pouvez aussi exporter la variable d'environnement pour la session en cours :

```bash
export FRANCOISDB_DRIVER=mysql
# ou
export FRANCOISDB_DRIVER=sqlite
```

Le bootstrap des tests (`francoisdcls/tests/bootstrap.php`) crée un fichier `francoisdcls/var/test_db.sqlite` propre pour les tests et le supprime/récrée à chaque exécution. Utilisez `francoisdcls/var/create_sqlite.php` pour (re)générer localement le fichier SQLite si besoin.

## Changelog

Un journal des modifications pour ce module est disponible dans `francoisdcls/CHANGELOG.md`. Ce fichier contient les notes de version, les réorganisations importantes et les décisions de configuration relatives à ce module. Consultez également `francoisdcls/IMPROVEMENTS.md` pour la liste des améliorations et plan d'actions.
