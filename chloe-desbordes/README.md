# UTMB - Gestion des coureurs et courses

Ce site permet de gérer et d'afficher les informations liées aux coureurs, aux courses, aux participations et aux points ITRA pour l'événement UTMB.

## Fonctionnalités principales
- Affichage de la liste des coureurs
- Affichage des courses
- Affichage des participations
- Affichage des points ITRA
- Affichage des nationalités
- Ajout de nouveaux coureurs
- Architecture claire séparant l'affichage (HTML/PHP) et les services d'accès à la base de données

## Organisation des fichiers
- `index.php` : page d'accueil du site
- `connexion.php` : gestion de la connexion à la base de données via PDO
- `assets/` : ressources statiques (CSS, JS, images)
- `pages/` : pages d'affichage (HTML et PHP)
- `services/` : scripts PHP fournissant les données de la base au format JSON (API)

## Accès aux données
Les pages PHP utilisent PDO pour interroger la base de données MySQL et afficher dynamiquement les informations. Les fichiers de service peuvent être utilisés pour des appels AJAX côté client.

## Auteur
Chloé Desbordes

## Remarques
- Pour toute modification de la structure, adapter les liens dans les fichiers HTML/PHP.
- La connexion à la base de données est centralisée dans `connexion.php`.
