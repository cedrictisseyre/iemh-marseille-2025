# 🚀 Guide de démarrage rapide - Dev Container

Ce projet dispose maintenant d'un environnement de développement containerisé qui permet de tester facilement le code PHP avec MySQL et phpMyAdmin.

## 📋 Prérequis

1. **Visual Studio Code** - [Télécharger](https://code.visualstudio.com/)
2. **Docker Desktop** - [Télécharger](https://www.docker.com/products/docker-desktop/)
3. **Extension Dev Containers** pour VS Code - [Installer](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

## 🎯 Démarrage rapide

### Étape 1 : Ouvrir le projet
```bash
git clone https://github.com/cedrictisseyre/iemh-marseille-2025.git
cd iemh-marseille-2025
code .
```

### Étape 2 : Lancer le Dev Container

1. Dans VS Code, appuyez sur **F1** (ou Cmd+Shift+P sur Mac)
2. Tapez et sélectionnez : **Dev Containers: Reopen in Container**
3. Attendez que le container se construise (première fois : 5-10 minutes)

### Étape 3 : Accéder aux services

Une fois le container démarré :

| Service | URL | Identifiants |
|---------|-----|--------------|
| **phpMyAdmin** | http://localhost:8081 | User: `root` / Pass: `root_password` |
| **Applications PHP** | http://localhost:8080 | - |
| **MySQL** | localhost:3306 | User: `root` / Pass: `root_password` |

### Étape 4 : Tester votre code PHP

1. Placer vos fichiers PHP dans votre dossier personnel
2. Ouvrir votre navigateur : `http://localhost:8080/votre-dossier/fichier.php`
3. Exemple : `http://localhost:8080/evan-benoit/index.php`

## 🔧 Connexion à la base de données

Dans vos fichiers PHP, utilisez ces paramètres pour le Dev Container :

```php
<?php
$host = 'mysql';  // Nom du service (pas localhost)
$user = 'root';
$password = 'root_password';
$dbname = 'iemh_dev';  // ou votre propre base

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
?>
```

> 💡 **Astuce** : Consultez le fichier `.devcontainer/connexion-exemple.php` pour un exemple complet.

## 📚 Documentation complète

Pour plus de détails, consultez : [.devcontainer/README.md](.devcontainer/README.md)

## ❓ Problèmes courants

### Le container ne démarre pas
- Vérifiez que Docker Desktop est démarré
- Vérifiez que les ports 3306, 8080 et 8081 sont libres

### Erreur de connexion MySQL
- Utilisez `mysql` comme host (pas `localhost`)
- Attendez 30 secondes après le démarrage du container

### phpMyAdmin ne charge pas
- Vérifiez que MySQL est démarré : voir les logs Docker
- Rafraîchissez la page après quelques secondes

## 🆘 Support

Pour toute question :
- Consultez la documentation : `.devcontainer/README.md`
- Contactez : cedric.TISSEYRE@univ-amu.fr
- Ouvrez une issue sur GitHub

---

**Bon développement ! 🎉**
