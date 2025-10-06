# Site F1 - Fiches Pilotes et Statistiques

Ce projet propose un site web dynamique permettant de consulter des fiches d'informations sur les pilotes de Formule 1, leurs statistiques et d'autres données issues d'une base MySQL.

## Fonctionnalités principales
- Affichage de la liste des champions du monde de F1
- Détail des victoires et années de titres pour chaque pilote
- (À venir) Recherche de pilotes, statistiques avancées, comparaisons, fiches détaillées, etc.

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
├── assets/         # Images, CSS, JS (à créer)
├── database/       # Connexion et scripts SQL
├── pages/          # Pages HTML/PHP (fiches, listes, formulaires)
├── services/       # Scripts PHP pour accès AJAX/API
├── sites/          # Pages principales (ex: pantheon_pilotes.php)
```

## Utilisation
- Accéder à la page principale pour voir la liste des champions
- (À venir) Utiliser les fonctionnalités de recherche, statistiques, etc.

## Auteur
Projet IEMH Marseille 2025 - François Duclos
