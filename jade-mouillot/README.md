# Application de gestion sportive

Cette application web permet de gérer dynamiquement les sportifs, clubs, courses et disciplines d'une base de données MySQL via une interface PHP simple et moderne.

## Fonctionnalités principales

- **Ajout et affichage dynamiques** des sportifs, clubs, courses et disciplines
- **Gestion des relations** (clés étrangères) entre sportifs, clubs, courses et disciplines
- **Navigation rapide** entre les différentes entités sans repasser par le menu principal
- **Interface responsive** et facile à utiliser

## Structure du projet

```
jade-mouillot/
├── php-admin/
│   ├── db_connect.php         # Connexion à la base de données
│   └── pages/
│       ├── sportif.php       # Gestion des sportifs (ajout + liste)
│       ├── club.php          # Gestion des clubs (ajout + liste)
│       ├── course.php        # Gestion des courses (ajout + liste)
│       └── discipline.php    # Gestion des disciplines (ajout + liste)
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

## Technologies utilisées

- PHP (PDO pour la connexion MySQL)
- HTML/CSS (interface simple et responsive)
- MySQL/MariaDB

## Auteur
Projet réalisé par [cedrictisseyre] et collaborateurs dans le cadre du module IEMH Marseille 2025.
