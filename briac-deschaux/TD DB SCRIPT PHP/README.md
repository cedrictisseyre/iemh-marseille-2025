# NFL Stats Analyzer

Projet universitaire visant à créer une base de données MySQL connectée à une interface web en PHP pour gérer les joueurs NFL, leurs équipes et leurs statistiques.

## 🚀 Fonctionnalités
- Ajout d'un joueur avec ses informations principales (nom, prénom, âge, taille, poids, équipe).
- Ajout des statistiques d’un joueur pour la saison en cours.
- Affichage et classement des joueurs par conférence selon leurs performances.
- Interface sobre avec navigation par onglets.

## 🛠️ Technologies
- PHP 8
- MySQL / phpMyAdmin
- HTML5 / CSS3
- Hébergé localement via XAMPP (ou autre serveur Apache/PHP)

## ⚙️ Installation
1. Cloner ou télécharger le projet dans le dossier `htdocs` de XAMPP.
2. Créer une base de données `briac_deschaux` sous phpMyAdmin.
3. Importer le fichier SQL fourni (`database.sql`) pour créer les tables.
4. Modifier `config.php` si besoin pour adapter l’hôte, l’utilisateur et le mot de passe de ta BDD.
5. Accéder au site via : [http://localhost/nfl-stats](http://localhost/nfl-stats)

## 📖 Utilisation
- **Onglet Joueurs** : ajouter ou consulter les joueurs.
- **Onglet Statistiques** : renseigner les performances d’un joueur pour la saison.
- **Onglet Classement** : consulter la hiérarchie des joueurs par conférence.

## 👤 Auteur
- Briac Deschaux  
- Étudiant ENSIACET – Projet PHP/MySQL
