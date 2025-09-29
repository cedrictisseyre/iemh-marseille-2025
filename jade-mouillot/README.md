# Application de gestion sportive

Cette application web permet de gérer dynamiquement les sportifs, clubs, courses et disciplines d'une base de données MySQL via une interface PHP simple et moderne.


## Fonctionnalités principales

- **Ajout et affichage dynamiques** des sportifs, clubs, courses et disciplines
- **Gestion des relations** (clés étrangères) entre sportifs, clubs, courses et disciplines
- **Historique des clubs** : chaque sportif peut avoir plusieurs clubs successifs, avec dates de début et de fin (table `club_membership`).
- **Changement de club** : possibilité de clôturer l’adhésion courante et d’en créer une nouvelle depuis l’interface.
- **Affichage de l’historique** : visualisation de tous les clubs précédents d’un sportif, avec périodes d’adhésion.
- **Navigation rapide** entre les différentes entités sans repasser par le menu principal
- **Interface responsive** et facile à utiliser

## Structure du projet

```
jade-mouillot/
├── php-admin/
│   ├── db_connect.php         # Connexion à la base de données
│   └── pages/
│       ├── gestion_sportif.php       # Gestion des sportifs (ajout + liste + historique clubs)
│       ├── gestion_club.php          # Gestion des clubs (ajout + liste)
│       ├── gestion_course.php        # Gestion des courses (ajout + liste)
│       └── gestion_discipline.php    # Gestion des disciplines (ajout + liste)
```

## Utilisation

1. **Configurer la base de données**
   - Renseigner les identifiants dans `php-admin/db_connect.php`.
   - Importer le script SQL fourni pour créer les tables nécessaires.

2. **Accéder à l'application**
   - Ouvrir l'une des pages dans `php-admin/pages/` (ex : `sportif.php`).
   - Utiliser la barre de navigation pour passer d'une entité à l'autre.


3. **Ajouter ou consulter des données**
   - Remplir le formulaire pour ajouter un élément.
   - La liste s'actualise automatiquement.
   - Pour chaque sportif, cliquer sur « Voir » dans la colonne « Historique clubs » pour consulter ou modifier ses clubs successifs.

## Technologies utilisées

- PHP (PDO pour la connexion MySQL)
- HTML/CSS (interface simple et responsive)
- MySQL/MariaDB

## Auteur
Projet réalisé par [cedrictisseyre] et collaborateurs dans le cadre du module IEMH Marseille 2025.
