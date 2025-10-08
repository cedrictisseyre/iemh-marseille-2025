
# Application Strava Like - Benjamin Lemaire

Ce projet est une application web inspirée de Strava permettant de gérer et consulter les activités sportives, les utilisateurs, les équipements et les performances.

## Fonctionnalités principales
- Interface graphique moderne et responsive (HTML, CSS, JavaScript)
- Affichage dynamique de la liste des activités sportives
- Ajout, modification et suppression d'activités
- Gestion complète des utilisateurs (ajout, modification, suppression avec confirmation)
- Suppression en cascade : lorsqu'un utilisateur est supprimé, toutes ses activités, équipements et données associées sont également supprimées
- Liaison avec une base de données MySQL/MariaDB
- Structure évolutive pour ajouter des fonctionnalités (gestion des équipements, performances, etc.)

## Structure du dossier
- `index.php` : page d'accueil et affichage des activités
- `add_activity.php` : ajout d'une activité sportive
- `users.php` : gestion des utilisateurs (ajout, modification, suppression)
- `edit_user.php` : modification d'un utilisateur
- `config.php` : configuration de la connexion à la base de données
- `css/` : styles CSS de l'application
- `js/` : scripts JavaScript

## Installation
1. Importez la structure SQL fournie dans votre base de données MariaDB/MySQL.
2. Configurez les accès à la base dans `config.php` (hôte, base, utilisateur, mot de passe).
3. Placez-vous dans le dossier `benjamin-lemaire` et ouvrez `index.php` dans votre navigateur ou sur votre serveur local.

## Auteur
Benjamin Lemaire

---
Pour toute question ou amélioration, contactez le responsable du projet.
