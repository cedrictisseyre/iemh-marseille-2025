# Projet Base de Données — Coaching Sportif

Ce projet contient une base de données MySQL pour un système de gestion de coaching sportif.  
Il est prévu pour être utilisé avec PHP et peut être lié à un projet GitHub.

---

## 📂 Structure de la base de données

La base s'appelle : **`evan_benoit`**

### Tables

1. **`coachs`**
   - `id` (INT, clé primaire, auto-incrément)
   - `prenom` (VARCHAR)
   - `specialite` (VARCHAR)

2. **`clients`**
   - `id` (INT, clé primaire, auto-incrément)
   - `prenom` (VARCHAR)
   - `nom` (VARCHAR)
   - `age` (INT)

3. **`seances`**
   - `id` (INT, clé primaire, auto-incrément)
   - `date_seance` (DATE)
   - `type_seance` (VARCHAR)
   - `id_client` (INT, clé étrangère vers `clients.id`)
   - `id_coach` (INT, clé étrangère vers `coachs.id`)

---

## 🛠 Installation

1. **Créer la base de données**  
   Dans phpMyAdmin, crée la base `evan_benoit`.

2. **Exécuter le script SQL**  
   Copie le contenu du fichier `structure.sql` (ou le script fourni dans ce projet) dans l’onglet SQL de phpMyAdmin et exécute-le.

3. **Vérifier les tables**  
   Tu dois voir les tables : `coachs`, `clients`, `seances`.

4. **Connecter PHP à la base de données**  
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
