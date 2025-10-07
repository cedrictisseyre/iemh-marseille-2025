<?php
declare(strict_types=1);

// Debug en dev
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php';

$page = $_GET['page'] ?? 'joueurs';

// --- Helpers
define('CURRENT_YEAR', (int)date('Y'));

if (!function_exists('e')) {
    function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

function column_exists(PDO $pdo, string $col): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stats' AND COLUMN_NAME = :c");
    $stmt->execute([':c'=>$col]);
    return (bool)$stmt->fetchColumn();
}

function first_existing_column(PDO $pdo, array $candidates): ?string {
    foreach ($candidates as $c) if (column_exists($pdo,$c)) return $c;
    return null;
}

// mapping FR -> EN
$stat_map = [
  'yards_passe' => ['passing_yards','yards_passe'],
  'td_passe' => ['passing_tds','td_passe'],
  'interceptions'=> ['interceptions'],
  'yards_course'=> ['rushing_yards','yards_course'],
  'td_course'   => ['rushing_tds','td_course'],
  'receptions'  => ['receptions'],
  'yards_reception'=>['receiving_yards','yards_reception'],
  'td_reception'=>['receiving_tds','td_reception'],
  'plaquages'   => ['tackles','plaquages'],
  'sacks'       => ['sacks'],
  'interceptions_def'=>['interceptions_def']
];

$stat_column_map=[];
foreach ($stat_map as $k=>$cands) $stat_column_map[$k]=first_existing_column($pdo,$cands);

// Positions
$positions=[];
try {
  if ($pdo->query("SHOW TABLES LIKE 'position'")->fetchColumn()) {
    $positions=$pdo->query("SELECT id, code, libelle FROM position ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $rows=$pdo->query("SELECT DISTINCT poste FROM player WHERE poste<>''")->fetchAll(PDO::FETCH_COLUMN);
    foreach($rows as $r) $positions[]=['code'=>$r];
  }
}catch(Throwable $e){$positions=[];}

// Teams
$teams_grouped=[]; $teams_flat=[];
try{
  $rows=$pdo->query("SELECT id_team, nom_team, logo_url, conference, division FROM team ORDER BY conference,division,nom_team")->fetchAll(PDO::FETCH_ASSOC);
  foreach($rows as $t){
    $teams_grouped[$t['conference']][$t['division']][]=$t;
    $teams_flat[]=$t;
  }
}catch(Throwable $e){}

// --- HTML
?><!doctype html>
<html lang="fr"><head>
<meta charset="utf-8">
<title>NFL Stats Analyzer</title>
<style>
.container{max-width:1200px;margin:1em auto;padding:1em;background:#fff;border-radius:10px}
.menu{display:flex;gap:.6em;margin-bottom:1em}
.menu a{padding:.5em .8em;border-radius:8px;text-decoration:none;background:#ddd;color:#222}
.menu a.active{background:#ef4444;color:#fff}
header{text-align:center}
header img{width:120px;display:block;margin:0 auto}
.table-responsive{overflow-x:auto}
table{border-collapse:collapse;width:100%}
th,td{border:1px solid #ccc;padding:6px;text-align:left}
th{background:#f0f0f0;cursor:pointer}
.filters{display:flex;gap:.5em;flex-wrap:wrap;align-items:center;margin-bottom:.8em}
.card{border:1px solid #eee;padding:1em;border-radius:8px;margin:.6em 0}
.logo-team{height:20px;vertical-align:middle;margin-right:4px}
</style>
</head><body>
<div class="container">
<header>
  <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="NFL">
  <h1>NFL STATS ANALYZER</h1>
</header>

<nav class="menu">
  <?php foreach(['joueurs'=>'Joueurs','stats'=>'Statistiques','ranking'=>'Classement'] as $k=>$lab): ?>
    <a href="?page=<?=e($k)?>" class="<?=($page===$k)?'active':''?>"><?=e($lab)?></a>
  <?php endforeach; ?>
</nav>

<main>
<?php if($page==='joueurs'): ?>
  <section class="card"><h2>Ajouter un joueur</h2>
    <form method="post" action="services/add_player.php">
      <input type="text" name="prenom" placeholder="Prénom" required>
      <input type="text" name="nom" placeholder="Nom" required>
      <select name="position_id" required>
        <option value="">-- Poste --</option>
        <?php foreach($positions as $pos): ?>
          <?php if(isset($pos['id'])): ?>
            <option value="<?=e($pos['id'])?>"><?=e($pos['libelle'].' ('.$pos['code'].')')?></option>
          <?php else: ?>
            <option value="<?=e($pos['code'])?>"><?=e($pos['code'])?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      <input type="number" name="age" placeholder="Âge">
      <?=render_team_select_grouped($teams_grouped,'id_team')?>
      <button type="submit">Ajouter</button>
    </form>
  </section>

  <section class="card"><h2>Liste des joueurs</h2>
    <form method="get" class="filters">
      <input type="hidden" name="page" value="joueurs">
      <input type="search" name="q" placeholder="Rechercher nom ou prénom" value="<?=e($_GET['q']??'')?>">
      <select name="team_filter"><option value="">Toutes équipes</option>
        <?php foreach($teams_flat as $t): $sel=(($_GET['team_filter']??'')==$t['id_team'])?'selected':''; ?>
          <option value="<?=e($t['id_team'])?>" <?=$sel?>><?=e($t['nom_team'])?></option>
        <?php endforeach; ?>
      </select>
      <select name="position_filter"><option value="">Tous postes</option>
        <?php foreach($positions as $p): $val=$p['id']??$p['code']; $sel=(($_GET['position_filter']??'')==$val)?'selected':''; ?>
          <option value="<?=e($val)?>" <?=$sel?>><?=e($p['libelle']??$p['code'])?></option>
        <?php endforeach; ?>
      </select>
      <button>Filtrer</button>
    </form>

    <?php
      $where=[];$params=[];
      if(!empty($_GET['q'])){
        $parts=explode(' ',$_GET['q']);
        if(count($parts)>=2){
          $where[]='p.nom LIKE :nom AND p.prenom LIKE :prenom';
          $params[':nom']='%'.$parts[1].'%';
          $params[':prenom']='%'.$parts[0].'%';
        }else{
          $where[]='(p.nom LIKE :q OR p.prenom LIKE :q)';
          $params[':q']='%'.$_GET['q'].'%';
        }
      }
      if(!empty($_GET['team_filter'])){$where[]='p.id_team=:team';$params[':team']=$_GET['team_filter'];}
      if(!empty($_GET['position_filter'])){$where[]='COALESCE(pos.code,p.poste)=:pos';$params[':pos']=$_GET['position_filter'];}

      $sql="SELECT p.*,t.nom_team,t.logo_url,pos.code FROM player p
            LEFT JOIN team t ON p.id_team=t.id_team
            LEFT JOIN position pos ON p.position_id=pos.id";
      if($where) $sql.=' WHERE '.implode(' AND ',$where);
      $sql.=' ORDER BY p.nom LIMIT 500';
      $stmt=$pdo->prepare($sql);$stmt->execute($params);

      echo '<div class="grid">';
      while($pl=$stmt->fetch(PDO::FETCH_ASSOC)){
        echo '<div class="card">';
        echo '<h3>'.e($pl['prenom'].' '.$pl['nom']).'</h3>';
        echo '<p><strong>Équipe:</strong> '.($pl['logo_url']?'<img class="logo-team" src="'.e($pl['logo_url']).'">':'').e($pl['nom_team']).'</p>';
        echo '<p><strong>Poste:</strong> '.e($pl['code']??$pl['poste']).'</p>';
        echo '</div>';
      }
      echo '</div>';
    ?>
  </section>

<?php elseif($page==='stats'): ?>
  <h2>Statistiques</h2>
  <?php
  $groups=[
    'QB'=>['Quarterbacks',['prenom','nom','nom_team','saison','yards_passe','td_passe','interceptions']],
    'WR'=>['Wide Receivers',['prenom','nom','nom_team','saison','receptions','yards_reception','td_reception']],
    'RB'=>['Running Backs',['prenom','nom','nom_team','saison','yards_course','td_course','receptions']],
    'DB'=>['Defensive Backs',['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']],
    'LB'=>['Linebackers',['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']]
  ];

  foreach($groups as $code=>$info){list($label,$cols)=$info;echo "<h3>".e($label)."</h3>";
    $select=[];$display=[];
    foreach($cols as $c){
      if(in_array($c,['prenom','nom'])){$select[]="p.$c";$display[]=$c;}
      elseif($c==='nom_team'){$select[]='t.nom_team';$display[]='nom_team';}
      elseif($c==='saison'){$select[]='s.saison';$display[]='saison';}
      else{$db=$stat_column_map[$c]??null;$select[]=$db?"s.$db AS $c":"NULL AS $c";$display[]=$c;}
    }
    $sql="SELECT ".implode(',',$select)." FROM stats s
          JOIN player p ON p.id_player=s.id_player
          LEFT JOIN team t ON t.id_team=p.id_team
          WHERE COALESCE(p.poste,'') LIKE :pos ORDER BY s.saison DESC";
    $stmt=$pdo->prepare($sql);$stmt->execute([':pos'=>$code]);$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(!$rows){echo '<p>Aucune donnée</p>';continue;}
    echo "<div class='table-responsive'><table class='sortable'><thead><tr>";
    foreach($display as $d) echo "<th>".e(ucfirst(str_replace('_',' ',$d)))."</th>";
    echo "</tr></thead><tbody>";
    foreach($rows as $r){echo "<tr>";foreach($display as $d) echo "<td>".e($r[$d]??'')."</td>";echo "</tr>";}
    echo "</tbody></table></div>";
  }
  ?>

<?php elseif($page==='ranking'): ?>
  <h2>Classements</h2>
  <?php
  $col_td=[first_existing_column($pdo,['passing_tds','td_passe']),first_existing_column($pdo,['rushing_tds','td_course']),first_existing_column($pdo,['receiving_tds','td_reception'])];
  $expr_td=implode('+',array_map(fn($c)=>"COALESCE(s.$c,0)",array_filter($col_td)));
  $col_tackles=first_existing_column($pdo,['tackles','plaquages']);
  $expr_tackles=$col_tackles?"COALESCE(s.$col_tackles,0)":'0';

  $saison=$_GET['saison']??CURRENT_YEAR;
  $sql="SELECT p.prenom,p.nom,t.nom_team,t.logo_url,t.conference,$expr_td AS td,$expr_tackles AS plaquages
        FROM player p
        LEFT JOIN team t ON p.id_team=t.id_team
        LEFT JOIN stats s ON s.id_player=p.id_player AND s.saison=:s
        ORDER BY td DESC LIMIT 100";
  $stmt=$pdo->prepare($sql);$stmt->execute([':s'=>$saison]);$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
  echo "<h3>Classement par TD (".e($saison).")</h3>";
  echo "<div class='table-responsive'><table class='sortable'><thead><tr><th>Rang</th><th>Joueur</th><th>Équipe</th><th>TD</th></tr></thead><tbody>";
  $i=1;foreach($rows as $r){
    echo "<tr><td>$i</td><td>".e($r['prenom'].' '.$r['nom'])."</td><td>".($r['logo_url']?"<img class='logo-team' src='".e($r['logo_url'])."'>":"").e($r['nom_team'])."</td><td>".e($r['td'])."</td></tr>";
    $i++;}
  echo "</tbody></table></div>";

  $sql="SELECT p.prenom,p.nom,t.nom_team,t.logo_url,t.conference,$expr_tackles AS plaquages
        FROM player p
        LEFT JOIN team t ON p.id_team=t.id_team
        LEFT JOIN stats s ON s.id_player=p.id_player AND s.saison=:s
        ORDER BY plaquages DESC LIMIT 100";
  $stmt=$pdo->prepare($sql);$stmt->execute([':s'=>$saison]);$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
  echo "<h3>Classement par Plaquages (".e($saison).")</h3>";
  echo "<div class='table-responsive'><table class='sortable'><thead><tr><th>Rang</th><th>Joueur</th><th>Équipe</th><th>Plaquages</th></tr></thead><tbody>";
  $i=1;foreach($rows as $r){
    echo "<tr><td>$i</td><td>".e($r['prenom'].' '.$r['nom'])."</td><td>".($r['logo_url']?"<img class='logo-team' src='".e($r['logo_url'])."'>":"").e($r['nom_team'])."</td><td>".e($r['plaquages'])."</td></tr>";
    $i++;}
  echo "</tbody></table></div>";
  ?>
<?php endif; ?>
</main>
</div>
<script>
document.querySelectorAll('table.sortable').forEach(t=>{
  t.querySelectorAll('th').forEach((th,i)=>th.addEventListener('click',()=>{
    const rows=[...t.tBodies[0].rows];const asc=th.asc=!th.asc;
    rows.sort((a,b)=>{let A=a.cells[i].innerText,B=b.cells[i].innerText;
      if(!isNaN(A)&&!isNaN(B)){A=+A;B=+B;}return (A>B?1:-1)*(asc?1:-1);});
    rows.forEach(r=>t.tBodies[0].appendChild(r));
  }));
});
</script>
</body></html>
