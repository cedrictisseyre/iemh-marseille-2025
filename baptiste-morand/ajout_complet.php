<?php
require_once 'connexion.php';

// Traitement du formulaire
$message = '';
$saisons = $conn->query('SELECT id_saison, annee FROM saisons ORDER BY annee DESC')->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Saison : soit sélectionnée, soit ajoutée
        if (!empty($_POST['saison_existante'])) {
            $id_saison = intval($_POST['saison_existante']);
        } else {
            $annee = $_POST['annee'] ?? '';
            $desc = $_POST['description'] ?? '';
            $stmt = $conn->prepare('INSERT INTO saisons (annee, description) VALUES (?, ?)');
            $stmt->execute([$annee, $desc]);
            $id_saison = $conn->lastInsertId();
        }

        // Ajout match
        $date_match = $_POST['date_match'] ?? '';
        $adversaire = $_POST['adversaire'] ?? '';
        $lieu = $_POST['lieu'] ?? 'domicile';
        $stmt = $conn->prepare('INSERT INTO matchs (id_saison, date_match, adversaire, lieu) VALUES (?, ?, ?, ?)');
        $stmt->execute([$id_saison, $date_match, $adversaire, $lieu]);
        $id_match = $conn->lastInsertId();

        // Ajout stats pour chaque joueur
        for ($i = 0; $i < 12; $i++) {
            $nom = $_POST['joueur'][$i]['nom'] ?? '';
            $prenom = $_POST['joueur'][$i]['prenom'] ?? '';
            $poste = $_POST['joueur'][$i]['poste'] ?? '';
            if ($nom && $prenom) {
                // Vérifier si le joueur existe déjà
                $stmt = $conn->prepare('SELECT id_joueur FROM joueurs WHERE nom = ? AND prenom = ?');
                $stmt->execute([$nom, $prenom]);
                $id_joueur = $stmt->fetchColumn();
                if (!$id_joueur) {
                    $stmt = $conn->prepare('INSERT INTO joueurs (nom, prenom, poste) VALUES (?, ?, ?)');
                    $stmt->execute([$nom, $prenom, $poste]);
                    $id_joueur = $conn->lastInsertId();
                }
                // Ajout stats
                $pts = $_POST['joueur'][$i]['pts'] ?? 0;
                $reb_tot = $_POST['joueur'][$i]['reb_tot'] ?? 0;
                $ast = $_POST['joueur'][$i]['ast'] ?? 0;
                $stl = $_POST['joueur'][$i]['stl'] ?? 0;
                $blk = $_POST['joueur'][$i]['blk'] ?? 0;
                $turnovers = $_POST['joueur'][$i]['turnovers'] ?? 0;
                $pf = $_POST['joueur'][$i]['pf'] ?? 0;
                $stmt = $conn->prepare('INSERT INTO stats_match (id_match, id_joueur, pts, reb_tot, ast, stl, blk, turnovers, pf) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$id_match, $id_joueur, $pts, $reb_tot, $ast, $stl, $blk, $turnovers, $pf]);
            }
        }
        $message = 'Ajout réussi !';
    } catch (Exception $e) {
        $message = 'Erreur : ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout complet</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Ajout complet (saison, match, joueur, stats)</h1>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post">
        <fieldset>
            <legend>Saison</legend>
            <label for="saison_existante">Saison existante :</label>
            <select name="saison_existante" id="saison_existante">
                <option value="">-- Nouvelle saison --</option>
                <?php foreach ($saisons as $saison): ?>
                    <option value="<?php echo $saison['id_saison']; ?>"><?php echo htmlspecialchars($saison['annee']); ?></option>
                <?php endforeach; ?>
            </select><br>
            <div id="nouvelle_saison">
                Année : <input type="text" name="annee" placeholder="ex: 2025-2026"><br>
                Description : <input type="text" name="description"><br>
            </div>
        </fieldset>
        <fieldset>
            <legend>Match</legend>
            Date : <input type="date" name="date_match" required><br>
            Adversaire : <input type="text" name="adversaire" required><br>
            Lieu : <select name="lieu">
                <option value="domicile">Domicile</option>
                <option value="exterieur">Extérieur</option>
            </select><br>
        </fieldset>
        <fieldset>
            <legend>Statistiques des joueurs</legend>
            <table id="table-joueurs">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Poste</th>
                    <th>Points</th>
                    <th>Rebonds</th>
                    <th>Passes</th>
                    <th>Interceptions</th>
                    <th>Contres</th>
                    <th>Balles perdues</th>
                    <th>Fautes</th>
                </tr>
            </table>
            <button type="button" id="ajouter-joueur">Ajouter un joueur</button>
        </fieldset>
        <input type="submit" value="Ajouter">
    </form>
    <script>
    // Affiche/masque les champs de nouvelle saison selon la sélection
    document.getElementById('saison_existante').addEventListener('change', function() {
        document.getElementById('nouvelle_saison').style.display = this.value ? 'none' : 'block';
    });
    window.onload = function() {
        document.getElementById('nouvelle_saison').style.display = document.getElementById('saison_existante').value ? 'none' : 'block';

        // Ajout dynamique des lignes joueurs
        const table = document.getElementById('table-joueurs');
        let nbJoueurs = 9;
        const maxJoueurs = 13;
        function ajouterLigneJoueur(num) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${num+1}</td>
                <td><input type='text' name='joueur[${num}][nom]'></td>
                <td><input type='text' name='joueur[${num}][prenom]'></td>
                <td><input type='text' name='joueur[${num}][poste]'></td>
                <td><input type='number' name='joueur[${num}][pts]' min='0' value='0'></td>
                <td><input type='number' name='joueur[${num}][reb_tot]' min='0' value='0'></td>
                <td><input type='number' name='joueur[${num}][ast]' min='0' value='0'></td>
                <td><input type='number' name='joueur[${num}][stl]' min='0' value='0'></td>
                <td><input type='number' name='joueur[${num}][blk]' min='0' value='0'></td>
                <td><input type='number' name='joueur[${num}][turnovers]' min='0' value='0'></td>
                <td><input type='number' name='joueur[${num}][pf]' min='0' value='0'></td>`;
            table.appendChild(tr);
        }
        for (let i = 0; i < nbJoueurs; i++) {
            ajouterLigneJoueur(i);
        }
        document.getElementById('ajouter-joueur').addEventListener('click', function() {
            if (nbJoueurs < maxJoueurs) {
                ajouterLigneJoueur(nbJoueurs);
                nbJoueurs++;
            }
        });
    }
    </script>
    <a href="index.php">Retour à l'accueil</a>
</body>
</html>
