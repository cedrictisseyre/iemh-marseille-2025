# NFL Stats Analyzer

## Description

**NFL Stats Analyzer** est une application web interactive permettant de suivre et analyser les statistiques de joueurs de NFL. Le site est connecté à une base de données MySQL (via phpMyAdmin) et offre une interface simple pour :

- Ajouter des joueurs avec leurs informations (nom, prénom, poste, équipe, taille, poids, âge, année de début).  
- Ajouter des statistiques détaillées pour chaque joueur (yards, TDs, interceptions, plaquages, field goals, punts…).  
- Rechercher rapidement un joueur ou ses statistiques grâce à un système d’autocomplétion.  
- Consulter des classements par conférence ou par division selon différents critères (TDs, plaquages).  
- Visualiser les informations et stats des joueurs sous forme de cartes interactives avec animations et modal zoom.  

Ce projet a pour but de faciliter le suivi statistique des joueurs de NFL de manière dynamique et visuelle.

---

## Fonctionnalités principales

1. **Gestion des joueurs**
   - Ajouter de nouveaux joueurs via un formulaire complet.  
   - Sélection d’équipe et de poste via listes déroulantes.  

2. **Gestion des statistiques**
   - Ajouter des statistiques pour chaque joueur.  
   - Sélection du joueur via autocomplétion ou liste filtrable.  
   - Statistiques disponibles : passing yards, passing TDs, interceptions, rushing yards/TDs, receptions, receiving yards/TDs, plaquages, sacks, field goals, extra points, punts, etc.  

3. **Recherche**
   - Rechercher un joueur ou ses statistiques en tapant les premières lettres du nom ou prénom.  
   - Suggestions automatiques grâce à l’autocomplétion (JavaScript + PHP).  

4. **Classements**
   - Classement par conférence (total TDs) et par division (plaquages).  
   - Filtres par poste et équipe.  

5. **Interface interactive**
   - Cartes animées pour afficher joueurs et stats.  
   - Zoom modal sur les cartes pour plus de détails.  
   - Effets de scroll animés pour un rendu dynamique.  

---

## Arborescence du projet

briac-deschaux2/
├─ config/
│ └─ database_connexion.php # Connexion à la base de données via PDO
├─ css/
│ └─ style_page.css # Feuille de style principale
├─ services/
│ ├─ add_players.php # Script pour ajouter un joueur
│ ├─ add_stats.php # Script pour ajouter des statistiques
│ ├─ helpers.php # Fonctions utilitaires (ex : formatage)
│ └─ player_search.php # Script pour l'autocomplétion des joueurs
├─ NFL_stats_analyzer.php # Page principale de l'application
└─ README.md # Ce fichier

---

## Prérequis

- Serveur web compatible PHP (ex : Apache)  
- PHP 7.4 ou supérieur  
- Base de données MySQL / MariaDB  
- phpMyAdmin pour la gestion de la base  
- Navigateur moderne pour l’interface (Chrome, Firefox, Edge…)

---

## Installation

1. Cloner le projet depuis GitHub :

```bash
git clone https://github.com/ton-utilisateur/NFL_Stats_Analyzer.git
Configurer la base de données :
Créer une base MySQL (ex : nfl_stats).
Importer le script SQL fourni ou créer les tables suivantes : player, team, position, stats.
Mettre à jour config/database_connexion.php avec vos identifiants MySQL.
Déployer le projet sur un serveur local ou distant.
Utilisation
Ouvrir NFL_stats_analyzer.php dans un navigateur.
Naviguer entre les onglets :
Joueurs : ajouter un joueur ou rechercher un joueur existant.
Statistiques : ajouter des stats pour un joueur, rechercher stats existantes.
Classement : consulter les classements par conférence ou division, filtrer par poste/équipe.
Cliquer sur les cartes pour un zoom détaillé.
Technologies utilisées
PHP (PDO pour la base de données)
MySQL / MariaDB
HTML5 / CSS3
JavaScript (autocomplétion, animations, modal zoom)
Licence
Projet académique — usage libre pour apprentissage et démonstration.