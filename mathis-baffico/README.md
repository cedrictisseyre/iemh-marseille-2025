
# Calculateur DEJ — Mathis Baffico

Ce dossier contient un projet de calcul et de suivi des Dépenses Énergétiques Journalières (DEJ) pour différents utilisateurs.

## Fonctionnalités principales
- Calcul du métabolisme de base (MB) et des DEJ selon les données saisies (nom, sexe, âge, taille, poids, niveau d'activité).
- Enregistrement des utilisateurs et de leur historique de calculs.
- Affichage de l’historique des calculs par utilisateur.

## Structure BDD
- Table `utilisateurs` : stocke les informations personnelles (nom, sexe, âge, taille, poids).
- Table `calculs` : enregistre chaque calcul de DEJ (utilisateur, NAP, niveau d’activité, MB, DEJ, date).
- Connexion à la base via `connexion.php`.

## Sécurité
- Requêtes PDO préparées pour éviter les injections SQL.
- Validation et échappement des données utilisateurs (`htmlspecialchars`).
- Gestion des erreurs de connexion à la base de données.

## Fichiers clés
- `formulaire.php` : saisie des données et calcul du DEJ.
- `affichage.php` : affichage de l’historique des calculs par utilisateur.
- `connexion.php` : connexion à la base de données.

## Pour démarrer
1. Créez la base de données et les tables nécessaires (`utilisateurs`, `calculs`).
2. Adaptez les identifiants de connexion dans `connexion.php`.
3. Lancez `formulaire.php` pour saisir des données, puis consultez l’historique via `affichage.php`.

---
Projet réalisé dans le cadre de l’IEMH Marseille 2025
