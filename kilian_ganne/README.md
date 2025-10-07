# Golden World Trail Series (GTWS)

## Objectif du site

Ce site web permet de gérer et de visualiser les résultats de la Golden World Trail Series, un circuit international de trail running. Il propose une interface moderne et collaborative pour :

- Consulter le classement général des coureurs sur l'ensemble des manches.
- Visualiser les résultats de chaque manche (course).
- Rechercher un coureur pour afficher ses informations et ses performances.
- Ajouter de nouveaux coureurs à la base de données.

L'application utilise une base de données MySQL pour stocker les informations des coureurs et des résultats. Elle est conçue pour être facilement extensible et maintenable par une équipe de développeurs.

## Structure du projet

```
kilian_ganne/
├── GTWS.php                # Page principale de l'application
├── connexion_database.php  # Connexion à la base MySQL
├── GTWS_tables.sql         # Script SQL pour créer les tables
├── css/
│   └── style.css           # Feuille de style principale
├── img/
│   ├── logo_gtws.png       # Logo officiel GTWS
│   ├── course1.jpg         # Image de course 1
│   ├── course2.jpg         # Image de course 2
│   └── course3.jpg         # Image de course 3
├── js/                     # Scripts JavaScript (optionnel)
├── sql/                    # Autres scripts SQL (optionnel)
└── assets/                 # Ressources diverses
```

## Installation

1. Importez le script `GTWS_tables.sql` dans votre base MySQL.
2. Configurez les accès à la base dans `connexion_database.php`.
3. Placez le logo et les images de courses dans le dossier `img/`.
4. Lancez `GTWS.php` sur votre serveur PHP.

## Auteur
Projet réalisé par Kilian Ganne et collaborateurs pour l'IEMH Marseille 2025.
