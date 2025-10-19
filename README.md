# iemh-marseille-2025

## Objectif du projet
Ce dépôt regroupe les travaux réalisés par les étudiants de la formation IHME Marseille 2025 dans le cadre des Travaux Dirigés (TD). L’objectif est de développer des compétences en algorithme, programmation et gestion de base de données.

## Structure du dépôt
- Un dossier par étudiant, contenant ses créations et exercices réalisés pendant les TD
- Des fichiers PHP, HTML, JS, SQL selon les sujets abordés
- Un README pour chaque dossier étudiant (optionnel)

## Instructions pour lancer le projet

### 🌐 Environnement de production
Le projet est déployé automatiquement sur l’environnement Jelastic d’Infomaniak pour visualisation

Pour visualiser les résultats et accéder aux fonctionnalités, rendez-vous directement sur :
https://env-iemh.jcloud-ver-jpe.ik-server.com/

Aucune installation ou lancement manuel n’est nécessaire.

### 💻 Environnement de développement local (Dev Container)
Pour tester et développer localement avec PHP, MySQL et phpMyAdmin :

1. Installez [Docker Desktop](https://www.docker.com/products/docker-desktop/) et [VS Code](https://code.visualstudio.com/)
2. Installez l'extension [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)
3. Ouvrez le projet dans VS Code
4. Appuyez sur **F1** → **Dev Containers: Reopen in Container**
5. Accédez à phpMyAdmin : http://localhost:8081 (user: `root` / pass: `root_password`)

📘 **Guide complet** : [DEVCONTAINER_GUIDE.md](DEVCONTAINER_GUIDE.md)


## Modalités d’évaluation
- Les dossiers étudiants sont évalués sur :
  - Qualité des algorithmes
  - Fonctionnalité et clarté du code
  - Respect des consignes et des bonnes pratiques
  - Utilisation pertinente des bases de données
  - Structure de la base
- Les livrables attendus sont les fichiers et dossiers présents dans le dépôt

## Contacts
- Encadrant : Cédric Tisseyre (cedric.TISSEYRE@univ-amu.fr)
- Pour toute question, contacter l’encadrant ou utiliser les issues GitHub

## Liens utiles
- [Documentation PHP](https://www.php.net/manual/fr/)
- [Documentation MySQL](https://dev.mysql.com/doc/)
- [Documentation GitHub](https://docs.github.com/fr)

## Licence
Ce projet est à usage pédagogique uniquement.

## Changelog (résumé des actions récentes)

- 2025-10-19 : Nettoyage de fichiers de debug — suppression de `ando-guerin/test.php` et retrait des scripts temporaires identifiés dans `francoisdcls/devtools/` (si nécessaire).
- Recommandation : ne pas committer le dossier `vendor/` dans le dépôt. Si tu veux garder les dépendances, c'est ok, mais il est préférable d'ajouter `vendor/` au `.gitignore` et d'utiliser `composer install` en CI ou localement.

Si tu veux que j'applique un nettoyage plus large (suppression automatique de tous les `tmp_*.php` et fichiers de debug), demande-le explicitement et je le ferai après validation.