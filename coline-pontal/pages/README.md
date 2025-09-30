# Gestion Karaté — Coline Pontal

Ce dossier contient le projet de gestion de clubs, tournois et karateka pour le karaté.

## Fonctionnalités principales
- Liste des clubs, ajout de club (nom, ville, pays, date de création)
- Liste des tournois (championnats), ajout de tournoi (nom, lieu, date, type)
- Liste des karateka, ajout de karateka (nom, prénom, date de naissance, sexe, grade, club)
- Ajout de participation à un championnat (karateka, championnat, épreuve, sexe, équipe, catégorie, résultat)

## Structure BDD
- Table `club` : clubs de karaté
- Table `championnat` : tournois
- Table `karateka` : pratiquants
- Table `participation` : participations aux championnats

## Accessibilité & Qualité
- Navigation par onglets
- Formulaires accessibles (labels, focus visible)
- Contraste renforcé

## Sécurité
- Requêtes PDO préparées
- Échappement HTML avec `htmlspecialchars`

## Fichiers clés
- `gestion-karate.php` : page principale, navigation, formulaires et affichage
- `style-onglets.css` : styles, accessibilité
- `db_connexion.php` : connexion à la base de données

## Pour démarrer
1. Importer la structure SQL fournie dans votre SGBD
2. Adapter les identifiants dans `db_connexion.php`
3. Ouvrir `gestion-karate.php` dans le navigateur

---
Projet réalisé dans le cadre de l’IEMH Marseille 2025
