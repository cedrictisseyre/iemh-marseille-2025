# Configuration Dev Container pour IEMH Marseille 2025

Ce dossier contient la configuration Dev Container pour développer et tester les projets PHP avec MySQL et phpMyAdmin.

## 🚀 Utilisation

### Prérequis
- Visual Studio Code
- Extension "Dev Containers" installée dans VS Code
- Docker Desktop installé et démarré

### Démarrer le Dev Container

1. Ouvrir le projet dans VS Code
2. Appuyer sur `F1` et sélectionner `Dev Containers: Reopen in Container`
3. Attendre que le container soit construit et démarré (première fois peut prendre quelques minutes)

### Services disponibles

Une fois le container démarré, vous aurez accès à :

- **PHP 8.2** : Environnement de développement PHP avec toutes les extensions nécessaires
- **MySQL 8.0** : Serveur de base de données sur le port 3306
- **phpMyAdmin** : Interface web accessible sur http://localhost:8081
- **Apache + PHP** : Serveur web pour tester vos applications sur http://localhost:8080

### Connexion à la base de données

Pour se connecter à MySQL depuis votre code PHP dans le container :

```php
<?php
$host = 'mysql';  // Nom du service dans docker-compose
$user = 'root';
$password = 'root_password';
$dbname = 'iemh_dev';  // ou créer votre propre base

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
```

### Accès à phpMyAdmin

1. Ouvrir votre navigateur à l'adresse : http://localhost:8081
2. Se connecter avec :
   - **Utilisateur** : `root`
   - **Mot de passe** : `root_password`

### Tester vos applications PHP

1. Placer vos fichiers PHP dans le dossier du projet
2. Accéder à http://localhost:8080/nom-dossier/fichier.php
   - Exemple : http://localhost:8080/evan-benoit/index.php

### Commandes utiles dans le terminal du container

```bash
# Vérifier la version de PHP
php -v

# Lister les extensions PHP installées
php -m

# Se connecter à MySQL en ligne de commande
mysql -h mysql -u root -proot_password

# Installer des packages avec Composer (si nécessaire)
composer install
```

## 📝 Configuration

### Modifier les identifiants MySQL

Éditer le fichier `docker-compose.yml` et modifier les variables d'environnement :

```yaml
environment:
  MYSQL_ROOT_PASSWORD: votre_mot_de_passe
  MYSQL_DATABASE: votre_base
  MYSQL_USER: votre_utilisateur
  MYSQL_PASSWORD: votre_mot_de_passe_utilisateur
```

### Ajouter des extensions PHP

Éditer le fichier `Dockerfile` et ajouter les extensions souhaitées :

```dockerfile
RUN docker-php-ext-install nom_extension
```

## 🔧 Dépannage

### Le container ne démarre pas
- Vérifier que Docker Desktop est bien démarré
- Vérifier qu'aucun autre service n'utilise les ports 3306, 8080 ou 8081
- Reconstruire le container : `Dev Containers: Rebuild Container`

### Erreur de connexion MySQL
- Attendre quelques secondes que MySQL soit complètement démarré
- Vérifier les identifiants dans votre code PHP
- Utiliser `mysql` comme hostname (pas `localhost` ou `127.0.0.1`)

### phpMyAdmin ne se charge pas
- Vérifier que le service MySQL est bien démarré
- Attendre quelques secondes après le démarrage du container
- Vérifier dans les logs : `Docker: Show Container Log` > `phpmyadmin`

## 📚 Ressources

- [Documentation Dev Containers](https://code.visualstudio.com/docs/devcontainers/containers)
- [Documentation PHP](https://www.php.net/manual/fr/)
- [Documentation MySQL](https://dev.mysql.com/doc/)
- [Documentation phpMyAdmin](https://docs.phpmyadmin.net/)
