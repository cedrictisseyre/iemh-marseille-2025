# Statistiques de l'Équipe N3 du Montfort Basket Club

Ce module permet de gérer et consulter les statistiques des joueurs, des matchs et des saisons du Montfort Basket Club (N3).

## Fonctionnalités principales
- Accueil dynamique avec navigation
- Liste des joueurs et statistiques individuelles
- Liste des matchs et statistiques par match
- Liste des saisons
- Ajout complet d'un match et des stats de plusieurs joueurs en une seule opération

## Structure du dossier
- `index.php` : page d'accueil et navigation
- `ajout_complet.php` : formulaire d'ajout complet (saison, match, joueurs, stats)
- `connexion.php` : connexion à la base de données
- `assets/` : ressources (logo, styles, scripts)
- `joueurs/` : pages pour la gestion des joueurs
- `matchs/` : pages pour la gestion des matchs
- `saisons/` : pages pour la gestion des saisons

## Installation
1. Importez la structure SQL fournie dans votre base de données MariaDB/MySQL.
2. Configurez les accès à la base dans `connexion.php`.
3. Placez le logo du club dans `assets/logo-montfort.png`.
4. Accédez à `index.php` pour utiliser l'application.

## Auteur
Baptiste Morand

---
Pour toute question ou amélioration, contactez le responsable du projet.