# Gestion Rugby — Maëlle Goujat

Ce dossier contient le projet de gestion d’équipes, joueurs, matchs et statistiques pour le rugby.

## Fonctionnalités principales
- Liste des équipes, ajout d’équipe (nom, ville, pays)
- Liste des joueurs, ajout de joueur (nom, prénom, poste, équipe)
- Liste des matchs, ajout de match (date, lieu, équipes, scores)
- Statistiques individuelles par joueur et par match

## Structure BDD
- Table `equipes` : équipes de rugby
- Table `joueurs` : joueurs
- Table `matchs` : matchs
- Table `stats_joueurs` : statistiques individuelles

## Accessibilité & Qualité
- Interface moderne et responsive (CSS personnalisé)
- Formulaires accessibles (labels, focus visible)
- Contraste renforcé (bleu foncé)

## Sécurité
- Requêtes PDO préparées
- Échappement HTML avec `htmlspecialchars`

## Fichiers clés
- `index.php` : page principale, navigation, affichage dynamique
- `style-accueil.css` : styles, accessibilité
- `connexion.php` : connexion à la base de données
- `joueurs/ajouter_joueur.php` : ajout de joueur
- `equipes/ajouter_equipe.php` : ajout d’équipe

## Pour démarrer
1. Importer la structure SQL fournie dans votre SGBD
2. Adapter les identifiants dans `connexion.php`
3. Ouvrir `index.php` dans le navigateur

---
Projet réalisé dans le cadre de l’IHME Marseille 2025
