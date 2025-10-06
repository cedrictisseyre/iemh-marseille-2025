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
3. Importer le schéma et les données MySQL si besoin
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
