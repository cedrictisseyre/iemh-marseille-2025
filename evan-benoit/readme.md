# Projet Base de Données - EB Coaching

Ce projet contient une base de données MySQL pour un système de gestion de coaching sportif.  
Il est prévu pour être utilisé avec PHP et peut être lié à un projet GitHub.

---

## ⚠️ Usage

Ce site est exclusivement destiné au **gérant de la société de coaching** et aux **coachs** pour gérer les séances, les clients et le suivi de masse corporelle.  
Il **n’est pas destiné au grand public** ni aux **clients**.

---

## 📂 Structure de la base de données

La base s'appelle : **`evan_benoit`**

**Tables principales :**  
- `clients` : informations sur les clients (nom, prénom, âge, etc.)  
- `coachs` : informations sur les coachs (nom, prénom, spécialité, etc.)  
- `seances` : planning des séances (date, type, client, coach)  
- `suivi_masse` : suivi du poids/masse corporelle des clients  

---

## 🔗 Connexion à la base depuis PHP

Exemple de connexion (`connexion.php`) :

```php
<?php
$host = '195.15.235.20';
$user = 'root';
$password = 'VOTRE_MOT_DE_PASSE';
$dbname = 'evan_benoit';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Connexion réussie à la base de données !';
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
