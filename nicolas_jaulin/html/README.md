
# Gestion Rugby - Nicolas Jaulin

Ce dossier contient les pages PHP dynamiques pour la gestion d’un club de rugby :

- Équipes
- Joueurs
- Matchs
- Arbitres
- Associations matchs/arbitres
- Statistiques joueurs

## Arborescence

```
html/
├── index.html
├── db_connect.php
├── equipes/
│   ├── liste.php
│   ├── ajout.php
│   ├── modif.php
│   └── suppr.php
├── joueurs/
│   ├── liste.php
│   ├── ajout.php
│   ├── modif.php
│   └── suppr.php
├── matchs/
│   ├── liste.php
│   ├── ajout.php
│   ├── modif.php
│   └── suppr.php
├── arbitres/
│   ├── liste.php
│   ├── ajout.php
│   ├── modif.php
│   └── suppr.php
├── matchs_arbitres/
│   ├── liste.php
│   ├── ajout.php
│   └── suppr.php
├── statistiques/
│   ├── liste.php
│   ├── ajout.php
│   └── suppr.php
├── calculateur_vitesse.html
└── README.md
sql/
└── nicolas_jaulin.sql
```

## Utilisation

1. Lancer un serveur PHP dans le dossier `html` :
   ```bash
   php -S localhost:8000
   ```
2. Accéder aux pages via le navigateur (ex : http://localhost:8000/equipes/liste.php)
3. La base de données utilisée est `nicolas_jaulin` (voir `db_connect.php`).

## Dépendances
- PHP >= 7.4
- MySQL/MariaDB

## Auteur
Nicolas Jaulin
