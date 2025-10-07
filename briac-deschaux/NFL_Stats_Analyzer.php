<?php
require_once __DIR__ . '/config/db.php';

// Détermine la page affichée
$page = $_GET['page'] ?? 'joueurs';

// Récupération des saisons existantes
$stmt = $pdo->query("SELECT DISTINCT saison FROM stats ORDER BY saison DESC");
$saisons = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupération des équipes
$stmt = $pdo->query("SELECT id_team, nom_team FROM team ORDER BY nom_team");
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des postes distincts
$stmt = $pdo->query("SELECT DISTINCT poste FROM player ORDER BY poste");
$postes = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>NFL Stats Analyzer</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    nav a { margin-right: 15px; text-decoration: none; font-weight: bold; }
    nav a.active { text-decoration: underline; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { cursor: pointer; background: #f5f5f5; }
    form.filter { margin: 15px 0; }
  </style>
</head>
<body>
  <h1>NFL STATS ANALYZER</h1>
  <nav>
    <a href="?page=joueurs" class="<?= $page==='joueurs'?'active':'' ?>">Joueurs</a>
    <a href="?page=stats" class="<?= $page==='stats'?'active':'' ?>">Statistiques</a>
    <a href="?page=ranking" class="<?= $page==='ranking'?'active':'' ?>">Classement</a>
  </nav>

  <?php if ($page === 'joueurs'): ?>
    <h2>Ajouter un joueur</h2>
    <form method="post" action="services/add_player.php">
      <input type="text" name="prenom" placeholder="Prénom" required>
      <input type="text" name="nom" placeholder="Nom" required>
      <input type="number" name="age" placeholder="Âge">
      <input type="number" name="taille_cm" placeholder="Taille (cm)">
      <input type="number" name="poids_kg" placeholder="Poids (kg)">
      <input type="number" name="experience" placeholder="Expérience (années)">
      <select name="poste" required>
        <?php foreach ($postes as $poste): ?>
          <option value="<?= htmlspecialchars($poste) ?>"><?= htmlspecialchars($poste) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="id_team">
        <option value="">-- Équipe --</option>
        <?php foreach ($teams as $team): ?>
          <option value="<?= $team['id_team'] ?>"><?= htmlspecialchars($team['nom_team']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Ajouter</button>
    </form>

    <h2>Liste des joueurs</h2>
    <?php
      $stmt = $pdo->query("SELECT p.*, t.nom_team FROM player p LEFT JOIN team t ON p.id_team = t.id_team ORDER BY p.nom");
      $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="grid">
      <?php foreach ($players as $pl): ?>
        <div class="card">
          <h3><?= htmlspecialchars($pl['prenom'].' '.$pl['nom']) ?></h3>
          <p>Poste : <?= htmlspecialchars($pl['poste']) ?></p>
          <p>Équipe : <?= htmlspecialchars($pl['nom_team'] ?? 'Sans équipe') ?></p>
          <p>Âge : <?= htmlspecialchars($pl['age']) ?> ans</p>
          <p>Taille : <?= htmlspecialchars($pl['taille_cm']) ?> cm | Poids : <?= htmlspecialchars($pl['poids_kg']) ?> kg</p>
          <p>Expérience : <?= htmlspecialchars($pl['experience']) ?> ans</p>
        </div>
      <?php endforeach; ?>
    </div>

  <?php elseif ($page === 'stats'): ?>
    <h2>Statistiques par poste</h2>
    <?php foreach (["QB","RB","WR"] as $poste): ?>
      <h3><?= $poste ?></h3>
      <?php
        $sql = "SELECT p.prenom, p.nom, t.nom_team, s.*
                FROM stats s
                JOIN player p ON s.id_player = p.id_player
                LEFT JOIN team t ON p.id_team = t.id_team
                WHERE p.poste = :poste
                ORDER BY s.saison DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":poste"=>$poste]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <table class="sortable">
        <thead>
          <tr>
            <th>Joueur</th>
            <th>Équipe</th>
            <th>Saison</th>
            <?php if ($poste==="QB"): ?>
              <th>Yards Passés</th><th>TD</th><th>INT</th>
            <?php elseif ($poste==="RB"): ?>
              <th>Yards Course</th><th>TD</th>
            <?php elseif ($poste==="WR"): ?>
              <th>Réceptions</th><th>Yards Réception</th><th>TD</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']) ?></td>
              <td><?= htmlspecialchars($r['nom_team']) ?></td>
              <td><?= htmlspecialchars($r['saison']) ?></td>
              <?php if ($poste==="QB"): ?>
                <td><?= htmlspecialchars($r['yards_passe']) ?></td>
                <td><?= htmlspecialchars($r['td_passe']) ?></td>
                <td><?= htmlspecialchars($r['interceptions']) ?></td>
              <?php elseif ($poste==="RB"): ?>
                <td><?= htmlspecialchars($r['yards_course']) ?></td>
                <td><?= htmlspecialchars($r['td_course']) ?></td>
              <?php elseif ($poste==="WR"): ?>
                <td><?= htmlspecialchars($r['receptions']) ?></td>
                <td><?= htmlspecialchars($r['yards_reception']) ?></td>
                <td><?= htmlspecialchars($r['td_reception']) ?></td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endforeach; ?>

  <?php elseif ($page === 'ranking'): ?>
    <h2>Classement</h2>
    <form method="get" class="filter">
      <input type="hidden" name="page" value="ranking">
      Saison : <select name="saison">
        <?php foreach ($saisons as $s): ?>
          <option value="<?= $s ?>" <?= (($_GET['saison'] ?? '')==$s)?'selected':'' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      Poste : <select name="poste">
        <option value="">-- Tous --</option>
        <?php foreach ($postes as $p): ?>
          <option value="<?= $p ?>" <?= (($_GET['poste'] ?? '')==$p)?'selected':'' ?>><?= $p ?></option>
        <?php endforeach; ?>
      </select>
      Équipe : <select name="team">
        <option value="">-- Toutes --</option>
        <?php foreach ($teams as $t): ?>
          <option value="<?= $t['id_team'] ?>" <?= (($_GET['team'] ?? '')==$t['id_team'])?'selected':'' ?>><?= htmlspecialchars($t['nom_team']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Filtrer</button>
    </form>

    <?php
      $sql = "SELECT p.prenom, p.nom, p.poste, t.nom_team, s.*
              FROM stats s
              JOIN player p ON p.id_player = s.id_player
              LEFT JOIN team t ON t.id_team = p.id_team
              WHERE 1=1";
      $params = [];
      if (!empty($_GET['saison'])) { $sql .= " AND s.saison = :saison"; $params[':saison'] = $_GET['saison']; }
      if (!empty($_GET['poste']))  { $sql .= " AND p.poste = :poste";   $params[':poste']  = $_GET['poste']; }
      if (!empty($_GET['team']))   { $sql .= " AND t.id_team = :team"; $params[':team']   = $_GET['team']; }

      $sql .= " ORDER BY s.yards_passe DESC, s.yards_course DESC, s.yards_reception DESC, s.td_passe DESC, s.td_course DESC, s.td_reception DESC LIMIT 50";

      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table class="sortable">
      <thead>
        <tr>
          <th>Joueur</th><th>Poste</th><th>Équipe</th><th>Saison</th><th>Yards</th><th>TD</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ranking as $r): ?>
          <?php
            $yards = max($r['yards_passe'],$r['yards_course'],$r['yards_reception']);
            $td = max($r['td_passe'],$r['td_course'],$r['td_reception']);
          ?>
          <tr>
            <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']) ?></td>
            <td><?= htmlspecialchars($r['poste']) ?></td>
            <td><?= htmlspecialchars($r['nom_team']) ?></td>
            <td><?= htmlspecialchars($r['saison']) ?></td>
            <td><?= htmlspecialchars($yards) ?></td>
            <td><?= htmlspecialchars($td) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <script>
    // Petit script JS pour trier les colonnes
    document.querySelectorAll("table.sortable th").forEach(th => {
      th.addEventListener("click", () => {
        const table = th.closest("table");
        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        const idx = Array.from(th.parentNode.children).indexOf(th);
        const asc = !th.classList.contains("asc");
        rows.sort((a,b) => {
          const A = a.children[idx].innerText;
          const B = b.children[idx].innerText;
          return (isNaN(A)||isNaN(B) ? A.localeCompare(B) : A-B) * (asc?1:-1);
        });
        tbody.innerHTML = "";
        rows.forEach(r=>tbody.appendChild(r));
        table.querySelectorAll("th").forEach(x=>x.classList.remove("asc","desc"));
        th.classList.add(asc?"asc":"desc");
      });
    });
  </script>
</body>
</html>
