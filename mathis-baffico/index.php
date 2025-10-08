<?php
// index.php
// Single-file app: DEJ, Users, Sessions (trainings + multiple exercises), editing sessions, styled modals, profile with nap selection
require_once 'connexion.php';


function json_body(){
$p = file_get_contents('php://input');
$d = json_decode($p, true);
return $d ?: [];
}


$action = $_GET['action'] ?? null;
if ($action){
try {
switch($action){
// --- REFERENCES ---
case 'get_refs':
$refs = ['niveaux'=>[], 'types'=>[]];
$rows = $pdo->query('SELECT id, code, libelle FROM ref_niveau_activite ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$defaults = [1.2,1.375,1.55,1.725,1.9];
foreach($rows as $r){
$nap = null;
if (isset($r['code']) && $r['code'] !== null){
$tmp = str_replace(',', '.', $r['code']);
if (is_numeric($tmp) && floatval($tmp) > 0) $nap = floatval($tmp);
}
if ($nap === null && !empty($r['libelle'])){
if (preg_match('/([0-9]+[\.,]?[0-9]*)/', $r['libelle'], $m)){
$nap = floatval(str_replace(',', '.', $m[1]));
}
}
if ($nap === null){
$pos = (int)$r['id'] - 1;
$nap = $defaults[$pos] ?? 1.2;
}
$refs['niveaux'][] = ['id'=>$r['id'],'code'=>$r['code'],'libelle'=>$r['libelle'],'nap'=>$nap];
}
$rows2 = $pdo->query('SELECT id, libelle FROM ref_type_seance ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows2 as $r) $refs['types'][] = $r;
echo json_encode(['ok'=>true,'data'=>$refs]);
break;

// --- USERS ---
case 'list_users':
$stmt = $pdo->query('SELECT id, nom, sexe, age, poids, taille, date_inscription FROM utilisateurs ORDER BY nom');
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;


case 'add_user':
$d = json_body();
$stmt = $pdo->prepare('INSERT INTO utilisateurs (nom,sexe,age,poids,taille,date_inscription) VALUES (:nom,:sexe,:age,:poids,:taille,NOW())');
$stmt->execute([
':nom'=>substr(trim($d['nom'] ?? ''),0,200),
':sexe'=>$d['sexe'] ?? 'Homme',
':age'=>(int)($d['age'] ?? 0),
':poids'=>(float)($d['poids'] ?? 0),
':taille'=>(float)($d['taille'] ?? 0),
]);
echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
break;


case 'delete_user':
$d = json_body();
$stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id=:id');
$stmt->execute([':id'=>(int)$d['id']]);
echo json_encode(['ok'=>true]);
break;

// --- EXERCISES (library) ---
case 'list_exercises':
$stmt = $pdo->query('SELECT * FROM exercices ORDER BY nom_exercice');
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;


case 'add_exercise':
$d = json_body();
$stmt = $pdo->prepare('INSERT INTO exercices (entrainement_id, nom_exercice, series, repetitions, charge) VALUES (NULL,:nom,:series,:repetitions,:charge)');
$stmt->execute([':nom'=>substr($d['nom'] ?? '',0,200),':series'=>(int)($d['series'] ?? 0),':repetitions'=>(int)($d['repetitions'] ?? 0),':charge'=>(float)($d['charge'] ?? 0)]);
echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
break;


case 'update_exercise':
$d = json_body();
$stmt = $pdo->prepare('UPDATE exercices SET nom_exercice=:nom, series=:series, repetitions=:reps, charge=:charge WHERE id=:id');
$stmt->execute([':nom'=>substr($d['nom'] ?? '',0,200),':series'=>(int)($d['series'] ?? 0),':reps'=>(int)($d['repetitions'] ?? 0),':charge'=>(float)($d['charge'] ?? 0),':id'=>(int)$d['id']]);
echo json_encode(['ok'=>true]);
break;

// --- TRAININGS (sessions) ---
case 'add_training':
$d = json_body();
$user = (int)($d['utilisateur_id'] ?? 0);
$date = trim($d['date_seance'] ?? date('Y-m-d'));
$type_id = (int)($d['type_id'] ?? 0);
$type_label = substr($d['type_seance'] ?? '',0,100);
$duree = (int)($d['duree'] ?? 0);
$cal = (int)($d['calories_brulees'] ?? 0);
if (!$user || !$date) throw new Exception('Utilisateur et date requis');


$pdo->beginTransaction();
$stmt = $pdo->prepare('INSERT INTO entrainements (utilisateur_id, date_seance, type_seance, duree, calories_brulees, type_id) VALUES (:user,:date,:type,:duree,:cal,:type_id)');
$stmt->execute([':user'=>$user,':date'=>$date,':type'=>$type_label,':duree'=>$duree,':cal'=>$cal,':type_id'=>$type_id]);
$eid = $pdo->lastInsertId();


$exs = $d['exercises'] ?? [];
$ins = $pdo->prepare('INSERT INTO exercices (entrainement_id, nom_exercice, series, repetitions, charge) VALUES (:eid,:nom,:series,:reps,:charge)');
foreach($exs as $ex){
$nom = substr(trim($ex['nom_exercice'] ?? ''),0,200);
if (!$nom) continue;
$ins->execute([':eid'=>$eid,':nom'=>$nom,':series'=>(int)($ex['series'] ?? 0),':reps'=>(int)($ex['repetitions'] ?? 0),':charge'=>(float)($ex['charge'] ?? 0)]);
}
$pdo->commit();
echo json_encode(['ok'=>true,'id'=>$eid]);
break;

case 'list_trainings':
$user = (int)($_GET['user'] ?? 0);
if ($user){
$stmt = $pdo->prepare('SELECT e.*, r.libelle as type_lib FROM entrainements e LEFT JOIN ref_type_seance r ON e.type_id=r.id WHERE utilisateur_id=:user ORDER BY date_seance DESC');
$stmt->execute([':user'=>$user]);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
} else {
$stmt = $pdo->query('SELECT e.*, u.nom FROM entrainements e LEFT JOIN utilisateurs u ON e.utilisateur_id=u.id ORDER BY date_seance DESC');
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
}
break;


case 'get_training':
$id = (int)($_GET['id'] ?? 0);
if (!$id) throw new Exception('id requis');
$stmt = $pdo->prepare('SELECT e.*, u.nom FROM entrainements e LEFT JOIN utilisateurs u ON e.utilisateur_id=u.id WHERE e.id=:id');
$stmt->execute([':id'=>$id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt2 = $pdo->prepare('SELECT * FROM exercices WHERE entrainement_id=:id');
$stmt2->execute([':id'=>$id]);
$t['exercises'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'data'=>$t]);
break;

case 'update_training':
$d = json_body();
$id = (int)($d['id'] ?? 0);
if (!$id) throw new Exception('id requis');
$pdo->beginTransaction();
// update main training
$stmt = $pdo->prepare('UPDATE entrainements SET date_seance=:date, type_seance=:type, duree=:duree, calories_brulees=:cal, type_id=:type_id WHERE id=:id');
$stmt->execute([':date'=>$d['date_seance'],':type'=>substr($d['type_seance'] ?? '',0,100),':duree'=>(int)$d['duree'],':cal'=>(int)($d['calories_brulees'] ?? 0),':type_id'=> (int)($d['type_id'] ?? 0),':id'=>$id]);
// exercises: we expect list with either id (existing) or no id (new). We'll upsert: update if id, insert if not. Also process deletions array.
$existing = $pdo->prepare('SELECT id FROM exercices WHERE entrainement_id=:eid'); $existing->execute([':eid'=>$id]); $existingIds = array_column($existing->fetchAll(PDO::FETCH_ASSOC),'id');
$keep = [];
$ins = $pdo->prepare('INSERT INTO exercices (entrainement_id, nom_exercice, series, repetitions, charge) VALUES (:eid,:nom,:series,:reps,:charge)');
$upd = $pdo->prepare('UPDATE exercices SET nom_exercice=:nom, series=:series, repetitions=:reps, charge=:charge WHERE id=:id');
$exs = $d['exercises'] ?? [];
foreach($exs as $ex){
if(!empty($ex['id'])){
$upd->execute([':nom'=>substr($ex['nom_exercice'] ?? '',0,200),':series'=>(int)($ex['series'] ?? 0),':reps'=>(int)($ex['repetitions'] ?? 0),':charge'=>(float)($ex['charge'] ?? 0),':id'=>(int)$ex['id']]);
$keep[] = (int)$ex['id'];
} else {
$ins->execute([':eid'=>$id,':nom'=>substr($ex['nom_exercice'] ?? '',0,200),':series'=>(int)($ex['series'] ?? 0),':reps'=>(int)($ex['repetitions'] ?? 0),':charge'=>(float)($ex['charge'] ?? 0)]);
$keep[] = $pdo->lastInsertId();
}
}
// delete removed exercises
$toDelete = array_diff($existingIds, $keep);
if (!empty($toDelete)){
$in = implode(',', array_map('intval',$toDelete));
$pdo->exec("DELETE FROM exercices WHERE id IN ($in)");
}
$pdo->commit();
echo json_encode(['ok'=>true]);
break;

case 'delete_training':
$d = json_body();
$id = (int)($d['id'] ?? 0);
if (!$id) throw new Exception('id requis');
$pdo->beginTransaction();
$pdo->prepare('DELETE FROM exercices WHERE entrainement_id=:id')->execute([':id'=>$id]);
$pdo->prepare('DELETE FROM entrainements WHERE id=:id')->execute([':id'=>$id]);
$pdo->commit();
echo json_encode(['ok'=>true]);
break;


case 'delete_exercise':
$d = json_body();
$id = (int)($d['id'] ?? 0);
if (!$id) throw new Exception('id requis');
$pdo->prepare('DELETE FROM exercices WHERE id=:id')->execute([':id'=>$id]);
echo json_encode(['ok'=>true]);
break;

// --- CALCULS ---
case 'add_calc':
$d = json_body();
$user_id = isset($d['user_id']) ? (int)$d['user_id'] : 0;
if ($user_id){
$stmt = $pdo->prepare('SELECT id, nom, sexe, age, taille, poids FROM utilisateurs WHERE id=:id');
$stmt->execute([':id'=>$user_id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$u) throw new Exception('Utilisateur introuvable');
$nom = $u['nom']; $sexe = strtolower($u['sexe']); $age = (int)$u['age']; $taille = (float)$u['taille']; $poids = (float)$u['poids'];
} else {
$nom = trim($d['nom'] ?? '');
$sexe = strtolower(trim($d['sexe'] ?? ''));
$age = (int)($d['age'] ?? 0);
$taille = (float)($d['taille'] ?? 0);
$poids = (float)($d['poids'] ?? 0);
}
$nap = (float)($d['nap'] ?? 0);
if(!$nom || !in_array($sexe,['homme','femme']) || $age<=0 || $nap<=0) throw new Exception('Données invalides');
$sexe_aff = ($sexe==='homme')? 'Homme':'Femme';
$mb = ($sexe==='homme') ? (10*$poids)+(6.25*$taille)-(5*$age)+5 : (10*$poids)+(6.25*$taille)-(5*$age)-161;
$dej = $mb * $nap;


if ($user_id) $uid = $user_id; else {
$stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE nom=:nom AND sexe=:sexe AND age=:age AND taille=:taille AND poids=:poids LIMIT 1');
$stmt->execute([':nom'=>$nom,':sexe'=>$sexe_aff,':age'=>$age,':taille'=>$taille,':poids'=>$poids]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) $uid = $row['id']; else { $i = $pdo->prepare('INSERT INTO utilisateurs (nom,sexe,age,poids,taille,date_inscription) VALUES (:nom,:sexe,:age,:poids,:taille,NOW())'); $i->execute([':nom'=>$nom,':sexe'=>$sexe_aff,':age'=>$age,':poids'=>$poids,':taille'=>$taille]); $uid = $pdo->lastInsertId(); }
}
$niv = $d['niveau_activite'] ?? '';
$ins = $pdo->prepare('INSERT INTO calculs (utilisateur_id, nap, niveau_activite, metabolisme_base, dej, date_calcul) VALUES (:uid,:nap,:niv,:mb,:dej,NOW())');
$ins->execute([':uid'=>$uid,':nap'=>$nap,':niv'=>$niv,':mb'=>$mb,':dej'=>$dej]);
echo json_encode(['ok'=>true,'dej'=>$dej,'mb'=>$mb,'user_id'=>$uid]);
break;

case 'list_calcs':
$user = (int)($_GET['user'] ?? 0);
if (!$user) throw new Exception('user id required');
$stmt = $pdo->prepare('SELECT * FROM calculs WHERE utilisateur_id=:u ORDER BY date_calcul DESC LIMIT 100');
$stmt->execute([':u'=>$user]);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;

// --- STATS ---
case 'stats_dej':
$user = (int)($_GET['user'] ?? 0);
if (!$user) throw new Exception('user id required');
$stmt = $pdo->prepare('SELECT date_calcul, dej FROM calculs WHERE utilisateur_id=:u ORDER BY date_calcul ASC');
$stmt->execute([':u'=>$user]);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;


case 'stats_weekly_volume':
$user = (int)($_GET['user'] ?? 0);
if (!$user) throw new Exception('user id required');
$stmt = $pdo->prepare('SELECT YEARWEEK(date_seance,1) as yw, SUM(duree) as minutes FROM entrainements WHERE utilisateur_id=:u GROUP BY yw ORDER BY yw DESC LIMIT 12');
$stmt->execute([':u'=>$user]);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;

// --- PROFILE ---
case 'user_profile':
$user = (int)($_GET['user'] ?? 0);
if (!$user) throw new Exception('user required');
$stmt = $pdo->prepare('SELECT id, nom, sexe, age, poids, taille, date_inscription FROM utilisateurs WHERE id=:id');
$stmt->execute([':id'=>$user]); $u = $stmt->fetch(PDO::FETCH_ASSOC);
$s = $pdo->prepare('SELECT id, dej, date_calcul, nap, niveau_activite, metabolisme_base FROM calculs WHERE utilisateur_id=:u ORDER BY date_calcul DESC LIMIT 1'); $s->execute([':u'=>$user]); $last_dej = $s->fetch(PDO::FETCH_ASSOC);
$t = $pdo->prepare('SELECT id, date_seance, type_seance, duree, calories_brulees FROM entrainements WHERE utilisateur_id=:u ORDER BY date_seance DESC LIMIT 5'); $t->execute([':u'=>$user]); $trainings = $t->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'data'=>['user'=>$u,'last_dej'=>$last_dej,'trainings'=>$trainings]]);
break;


default:
echo json_encode(['ok'=>false,'error'=>'action inconnue']);
}
} catch (Exception $e){
if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
exit;
}

// --- FRONTEND HTML ---
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DEJ & Séances - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.tab{cursor:pointer}
.tab-active{border-bottom:3px solid #ef4444;color:#ef4444}
/* simple modal animations */
.modal-bg{transition:opacity .25s ease;}
.modal-card{transition:transform .25s ease, opacity .25s ease; transform:translateY(8px) scale(.98); opacity:0}
.modal-open .modal-card{transform:translateY(0) scale(1); opacity:1}
</style>
</head>
<body class="bg-gray-50 font-sans text-gray-800">
<header class="bg-gradient-to-r from-blue-700 to-red-600 text-white shadow-md">
<div class="max-w-6xl mx-auto px-4 flex justify-between items-center py-4">
<h1 class="text-2xl font-bold">🏋️ DEJ & Coaching</h1>
<nav class="flex space-x-4">
<button class="tab tab-active px-3 py-2" data-tab="dashboard">🏠 Tableau de bord</button>
<button class="tab px-3 py-2" data-tab="calcul">💪 Calcul DEJ</button>
<button class="tab px-3 py-2" data-tab="sessions">📅 Séances</button>
<button class="tab px-3 py-2" data-tab="users">👤 Utilisateurs</button>
</nav>
</div>
</header>
<main class="max-w-6xl mx-auto p-6">
<div id="alerts"></div>

<!-- DASHBOARD -->
<section id="dashboard" class="tab-content">
<h2 class="text-xl font-bold text-blue-700 mb-4">Tableau de bord</h2>
<div class="grid grid-cols-2 gap-6">
<div class="bg-white p-4 rounded shadow">
<h3 class="font-semibold">Évolution du DEJ</h3>
<select id="dash_user" class="border rounded p-2 mt-2"></select>
<canvas id="chart_dej" height="120"></canvas>
</div>
<div class="bg-white p-4 rounded shadow">
<h3 class="font-semibold">Volume hebdo (minutes)</h3>
<canvas id="chart_volume" height="120"></canvas>
</div>
</div>
</section>

<!-- CALCUL -->
<section id="calcul" class="tab-content hidden">
<h2 class="text-xl font-bold text-blue-700 mb-4">Calcul DEJ</h2>
<form id="form_calc" class="bg-white p-6 rounded shadow space-y-4">
<div class="flex gap-2 items-center">
<label class="font-semibold">Profil existant :</label>
<select id="calc_user_select" class="border p-2 rounded"><option value="">-- Aucun --</option></select>
<button id="clear_profile_calc" type="button" class="ml-2 text-sm text-gray-600">Effacer</button>
</div>
<div class="grid grid-cols-3 gap-4">
<input id="calc_nom" name="nom" placeholder="Nom" class="border p-2 rounded" required>
<select id="calc_sexe" name="sexe" class="border p-2 rounded" required>
<option value="homme">Homme</option>
<option value="femme">Femme</option>
</select>
<input id="calc_age" name="age" type="number" placeholder="Âge" class="border p-2 rounded" required>
</div>
<div class="grid grid-cols-3 gap-4">
<input id="calc_taille" name="taille" type="number" placeholder="Taille (cm)" class="border p-2 rounded" required>
<input id="calc_poids" name="poids" type="number" step="0.1" placeholder="Poids (kg)" class="border p-2 rounded" required>
<select id="select_nap" name="nap" class="border p-2 rounded" required></select>
</div>
<button class="bg-gradient-to-r from-blue-600 to-red-500 text-white px-4 py-2 rounded">Calculer</button>
</form>
<div id="result_calc" class="mt-4 hidden bg-green-50 p-4 rounded"></div>
</section>

<!-- SESSIONS (trainings + exercises) -->
<section id="sessions" class="tab-content hidden">
<h2 class="text-xl font-bold text-red-700 mb-4">Séances & Exercices</h2>
<div class="bg-white p-4 rounded shadow">
<form id="form_session" class="grid grid-cols-3 gap-3">
<select id="session_user" name="utilisateur_id" required></select>
<select id="session_type_id" name="type_id" required></select>
<input name="date_seance" type="date" required>
<input name="duree" placeholder="Durée (min)" type="number" required>
<input name="calories_brulees" placeholder="Calories brûlées" type="number">
<input name="type_seance" placeholder="Libellé (optionnel)" class="col-span-3">


<div class="col-span-3">
<h4 class="font-semibold mb-2">Exercices de la séance</h4>
<div id="exercises_container" class="space-y-2"></div>
<button id="add_ex_row" type="button" class="mt-2 bg-gray-200 p-2 rounded">+ Ajouter un exercice</button>
</div>


<button type="submit" class="col-span-3 bg-blue-600 text-white p-2 rounded">Créer la séance</button>
</form>
</div>
<div class="mt-4">
<h3 class="font-semibold">Dernières séances</h3>
<div id="list_sessions"></div>
</div>
</section>

<!-- USERS -->
<section id="users" class="tab-content hidden">
<h2 class="text-xl font-bold text-gray-800 mb-4">Utilisateurs</h2>
<div class="bg-white p-4 rounded shadow">
<form id="form_user" class="grid grid-cols-4 gap-2 mb-4">
<input name="nom" placeholder="Nom" required class="border p-2 rounded">
<select name="sexe" class="border p-2 rounded"><option value="Homme">Homme</option><option value="Femme">Femme</option></select>
<input name="age" placeholder="Âge" type="number" class="border p-2 rounded">
<input name="poids" placeholder="Poids" type="number" step="0.1" class="border p-2 rounded">
<input name="taille" placeholder="Taille" type="number" class="border p-2 rounded">
<button class="col-span-4 bg-indigo-600 text-white p-2 rounded">Ajouter utilisateur</button>
</form>
<div id="list_users" class="space-y-2"></div>
</div>


<!-- Profil detailed modal -->
<div id="user_profile_modal" class="modal-bg hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center p-4">
<div class="modal-card bg-white rounded p-4 max-w-2xl w-full">
<button id="close_profile" class="float-right">✖</button>
<div id="profile_content"></div>
</div>
</div>
</section>


<!-- Session edit modal -->
<div id="session_modal" class="modal-bg hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center p-4">
<div class="modal-card bg-white rounded p-4 max-w-2xl w-full">
<button id="close_session" class="float-right">✖</button>
<div id="session_content"></div>
</div>
</div>


</main>
<footer class="text-center py-6 text-gray-500 mt-12">© <?= date('Y') ?> DEJ Coaching</footer>

<script>
// Front-end logic
let refs = {};
let usersCache = [];
function showAlert(msg,type='green'){
const el=document.getElementById('alerts'); el.innerHTML=`<div class="p-2 my-2 rounded ${type==='green'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'}">${msg}</div>`;
setTimeout(()=>el.innerHTML='',3500);
}


async function api(action, opts={}){
const query = opts.query? ('&' + new URLSearchParams(opts.query)) : '';
const url = '?action=' + action + query;
const method = opts.method || 'GET';
const headers = opts.headers || {};
let body = opts.body;
if (body && typeof body !== 'string') { body = JSON.stringify(body); headers['Content-Type'] = 'application/json'; }
const res = await fetch(url, {method, headers, body});
return res.json();
}

async function loadRefs(){
const j = await api('get_refs');
if (j.ok){ refs = j.data; populateRefs(); }
}
function populateRefs(){
const napSel = document.getElementById('select_nap'); napSel.innerHTML='';
refs.niveaux.forEach(n=>{
const v = n.nap; const lab = n.libelle || n.code || ('NAP '+v);
napSel.innerHTML += `<option value="${v}">${lab} (NAP=${v})</option>`;
});
const typeSel = document.getElementById('session_type_id'); typeSel.innerHTML='';
refs.types.forEach(t=> typeSel.innerHTML += `<option value="${t.id}">${t.libelle}</option>`);
}


async function loadUsers(){
const j = await api('list_users'); if(!j.ok) return;
usersCache = j.data;
const sel = document.getElementById('session_user'); const dash = document.getElementById('dash_user'); const list = document.getElementById('list_users'); const calcSel = document.getElementById('calc_user_select');
sel.innerHTML=''; dash.innerHTML=''; list.innerHTML=''; calcSel.innerHTML = '<option value="">-- Aucun --</option>';
usersCache.forEach(u=>{
sel.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
dash.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
calcSel.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
list.innerHTML += `<div class="p-2 border rounded flex justify-between items-center"><div><strong class="cursor-pointer user-link" data-id="${u.id}">${u.nom}</strong><div class="text-sm text-gray-500">${u.age} ans • ${u.poids} kg • ${u.taille} cm</div></div><div><button class="del-user" data-id="${u.id}">🗑️</button></div></div>`;
});
attachUserHandlers();
}


function attachUserHandlers(){
document.querySelectorAll('.del-user').forEach(b=>b.onclick = async (e)=>{ const id=e.target.dataset.id; if(!confirm('Supprimer cet utilisateur ?')) return; const j=await api('delete_user',{method:'POST',body:{id}}); if(j.ok){ showAlert('Utilisateur supprimé'); loadUsers(); } else showAlert(j.error,'red'); });
document.querySelectorAll('.user-link').forEach(a=>a.onclick = async (e)=>{ const id = e.target.dataset.id; showProfile(id); });
}

// Exercises container
function addExerciseRow(container, data={}){
const c = container;
const row = document.createElement('div'); row.className='exercise-row grid grid-cols-4 gap-2 items-center';
row.innerHTML = `
<input name="nom_exercice" placeholder="Exercice" value="${(data.nom_exercice||'').replace(/"/g,'&quot;')}" class="border p-2 rounded">
<input name="series" placeholder="Séries" type="number" value="${data.series||''}" class="border p-2 rounded">
<input name="repetitions" placeholder="Répétitions" type="number" value="${data.repetitions||''}" class="border p-2 rounded">
<div class="flex gap-2"><input name="charge" placeholder="Charge" type="number" step="0.1" value="${data.charge||''}" class="border p-2 rounded"><button type="button" class="remove_ex p-2 rounded bg-red-100">✖</button></div>`;
c.appendChild(row);
row.querySelector('.remove_ex').onclick = ()=> row.remove();
return row;
}

async function loadSessions(userId=null){
const q = userId? ('?action=list_trainings&user='+userId) : '?action=list_trainings';
const res = await fetch(q); const j = await res.json();
const el = document.getElementById('list_sessions'); el.innerHTML=''; if(!j.ok) return;
j.data.forEach(s=>{
el.innerHTML += `<div class="p-2 border-b flex justify-between items-center"><div><strong>${s.type_lib || s.type_seance}</strong><div class="text-sm text-gray-500">${s.date_seance} • ${s.duree} min • ${s.calories_brulees || 0} kcal</div></div><div><button class="view-session mr-2" data-id="${s.id}">Voir</button><button class="edit-session mr-2" data-id="${s.id}">Modifier</button><button class="del-session text-red-600" data-id="${s.id}">Suppr</button></div></div>`;
});
document.querySelectorAll('.view-session').forEach(b=>b.onclick = async (e)=>{ const id=e.target.dataset.id; const j=await api('get_training',{query:{id}}); if(j.ok) showSessionModal(j.data,false); else showAlert(j.error,'red'); });
document.querySelectorAll('.edit-session').forEach(b=>b.onclick = async (e)=>{ const id=e.target.dataset.id; const j=await api('get_training',{query:{id}}); if(j.ok) showSessionModal(j.data,true); else showAlert(j.error,'red'); });
document.querySelectorAll('.del-session').forEach(b=>b.onclick = async (e)=>{ if(!confirm('Supprimer cette séance ?')) return; const id=e.target.dataset.id; const j=await api('delete_training',{method:'POST',body:{id}}); if(j.ok){ showAlert('Séance supprimée'); const uid = document.getElementById('session_user').value; loadSessions(uid); } else showAlert(j.error,'red'); });
}


function showSessionModal(data, editable=false){
const container = document.getElementById('session_content');
if (!editable){
let html = `<h3 class="text-lg font-bold">Séance ${data.date_seance}</h3><p>${data.type_seance} • ${data.duree} min</p><h4 class="mt-2 font-semibold">Exercices</h4>`;
if (data.exercises && data.exercises.length){ html += '<ul class="list-disc pl-6">' + data.exercises.map(ex=>`<li>${ex.nom_exercice} — ${ex.series}x${ex.repetitions} • ${ex.charge}</li>`).join('') + '</ul>'; }
else html += '<p class="text-sm text-gray-500">Aucun exercice enregistré</p>';
container.innerHTML = html;
const modal = document.getElementById('session_modal'); modal.classList.remove('hidden'); modal.classList.add('flex','modal-open');
return;
}
// editable form
let html = `<h3 class="text-lg font-bold">Modifier séance</h3>
<form id="edit_session_form" class="space-y-3">
<input type="hidden" name="id" value="${data.id}">
<div class="grid grid-cols-3 gap-2"><select name="type_id" class="border p-2 rounded" id="edit_type_id"></select><input name="date_seance" type="date" value="${data.date_seance}" class="border p-2 rounded"><input name="duree" type="number" value="${data.duree}" class="border p-2 rounded"></div>
<input name="calories_brulees" type="number" value="${data.calories_brulees||0}" class="border p-2 rounded">
<input name="type_seance" value="${(data.type_seance||'').replace(/"/g,'&quot;')}" class="border p-2 rounded w-full">
<h4 class="font-semibold">Exercices</h4>
<div id="edit_ex_container" class="space-y-2"></div>
<div class="flex gap-2"><button id="add_edit_ex" type="button" class="bg-gray-200 p-2 rounded">+ Ajouter exercice</button><button type="submit" class="bg-green-600 text-white p-2 rounded">Enregistrer</button></div>
</form>`;
container.innerHTML = html;
// populate type options
const sel = document.getElementById('edit_type_id'); sel.innerHTML=''; refs.types.forEach(t=> sel.innerHTML += `<option value="${t.id}" ${t.id==data.type_id? 'selected':''}>${t.libelle}</option>`);
// populate exercises
const exCont = document.getElementById('edit_ex_container');
(data.exercises||[]).forEach(ex=>{
const row = addExerciseRow(exCont, ex);
// store id on row for sending back
const hidden = document.createElement('input'); hidden.type='hidden'; hidden.name='ex_id'; hidden.value = ex.id; row.appendChild(hidden);
});
document.getElementById('add_edit_ex').addEventListener('click', ()=> addExerciseRow(exCont, {}));
document.getElementById('edit_session_form').addEventListener('submit', async (e)=>{
e.preventDefault();
const form = e.target; const fd = new FormData(form);
const payload = { id: data.id, type_id: parseInt(fd.get('type_id')||0), date_seance: fd.get('date_seance'), duree: parseInt(fd.get('duree')||0), calories_brulees: parseInt(fd.get('calories_brulees')||0), type_seance: fd.get('type_seance'), exercises: [] };
// gather exercise rows
document.querySelectorAll('#edit_ex_container .exercise-row').forEach(row=>{
const nom = row.querySelector('input[name="nom_exercice"]').value.trim(); if(!nom) return;
const series = parseInt(row.querySelector('input[name="series"]').value||0);
const reps = parseInt(row.querySelector('input[name="repetitions"]').value||0);
const charge = parseFloat(row.querySelector('input[name="charge"]').value||0);
const idInput = row.querySelector('input[name="ex_id"]'); const exId = idInput? parseInt(idInput.value||0):0;
const obj = { nom_exercise: nom, nom_exercice: nom, series, repetitions: reps, charge };
if (exId) obj.id = exId;
payload.exercises.push(obj);
});
// send update
const j = await api('update_training',{method:'POST',body:payload});
if(j.ok){ showAlert('Séance modifiée'); const uid = data.utilisateur_id || document.getElementById('session_user').value; loadSessions(uid); showProfile(uid); const modal = document.getElementById('session_modal'); modal.classList.add('hidden'); modal.classList.remove('flex','modal-open'); } else showAlert(j.error,'red');
});
const modal = document.getElementById('session_modal'); modal.classList.remove('hidden'); modal.classList.add('flex','modal-open');
}

// Profile
async function showProfile(userId){
const j = await api('user_profile',{query:{user:userId}});
if(!j.ok) return showAlert(j.error,'red');
const d = j.data; const container = document.getElementById('profile_content');
let html = `<div class="grid grid-cols-2 gap-4"><div><h2 class="text-xl font-bold">${d.user.nom}</h2><p>${d.user.age} ans • ${d.user.poids} kg • ${d.user.taille} cm</p><p class="mt-2">Dernier DEJ: ${d.last_dej? (Math.round(d.last_dej.dej)+' kcal • '+d.last_dej.date_calcul) : 'Aucun'}</p></div><div><button id="profile_view_sessions" class="bg-blue-600 text-white p-2 rounded">Voir toutes les séances</button></div></div>`;
html += '<h3 class="mt-4 font-semibold">Dernières séances</h3>';
if(d.trainings.length){ html += '<ul class="list-disc pl-6">' + d.trainings.map(t=>`<li>${t.date_seance} — ${t.type_seance} • ${t.duree} min <button data-id="${t.id}" class="ml-2 del-from-profile text-sm text-red-600">Suppr</button></li>`).join('') + '</ul>'; }
else html += '<p class="text-sm text-gray-500">Aucune séance</p>';
container.innerHTML = html;
const modal = document.getElementById('user_profile_modal'); modal.classList.remove('hidden'); modal.classList.add('flex','modal-open');


// attach delete handlers inside profile
container.querySelectorAll('.del-from-profile').forEach(btn=>btn.addEventListener('click', async (e)=>{ const id = e.target.dataset.id; if(!confirm('Supprimer cette séance ?')) return; const j = await api('delete_training',{method:'POST',body:{id}}); if(j.ok){ showAlert('Séance supprimée'); showProfile(userId); loadSessions(userId); } else showAlert(j.error,'red'); }));
const viewAll = document.getElementById('profile_view_sessions'); if(viewAll) viewAll.addEventListener('click', ()=>{ document.querySelector('[data-tab="sessions"]').click(); document.getElementById('session_user').value = userId; loadSessions(userId); const m = document.getElementById('user_profile_modal'); m.classList.add('hidden'); m.classList.remove('flex','modal-open'); });
}

// Charts
let chartDej=null, chartVolume=null;
async function drawDejChart(userId){ const j=await api('stats_dej',{query:{user:userId}}); if(!j.ok) return; const labels=j.data.map(x=>x.date_calcul); const data=j.data.map(x=>parseFloat(x.dej)); const ctx = document.getElementById('chart_dej').getContext('2d'); if(chartDej) chartDej.destroy(); chartDej = new Chart(ctx,{type:'line',data:{labels,datasets:[{label:'DEJ',data}]}}); }
async function drawVolumeChart(userId){ const j=await api('stats_weekly_volume',{query:{user:userId}}); if(!j.ok) return; const labels=j.data.map(x=>x.yw); const data=j.data.map(x=>parseInt(x.minutes)); const ctx = document.getElementById('chart_volume').getContext('2d'); if(chartVolume) chartVolume.destroy(); chartVolume = new Chart(ctx,{type:'bar',data:{labels,datasets:[{label:'Minutes/semaine',data}]}}); }


// Init
async function init(){
document.querySelectorAll('.tab').forEach(t=>t.addEventListener('click', e=>{ document.querySelectorAll('.tab').forEach(x=>x.classList.remove('tab-active')); e.target.classList.add('tab-active'); document.querySelectorAll('.tab-content').forEach(s=>s.classList.add('hidden')); document.getElementById(e.target.dataset.tab).classList.remove('hidden'); }));


await loadRefs(); await loadUsers();
await loadSessions();


const first = document.getElementById('dash_user').value; if(first){ drawDejChart(first); drawVolumeChart(first); }
document.getElementById('dash_user').addEventListener('change', e=>{ drawDejChart(e.target.value); drawVolumeChart(e.target.value); });


// add exercise row
document.getElementById('add_ex_row').addEventListener('click', ()=> addExerciseRow(document.getElementById('exercises_container')));
// default one row
addExerciseRow(document.getElementById('exercises_container'));
// session form submit
document.getElementById('form_session').addEventListener('submit', async (e)=>{
e.preventDefault();
const fd = new FormData(e.target); const body = Object.fromEntries(fd.entries());
const exCont = document.getElementById('exercises_container');
const exercises = [];
exCont.querySelectorAll('.exercise-row').forEach(row=>{
const nom = row.querySelector('input[name="nom_exercice"]').value.trim();
if(!nom) return;
exercises.push({ nom_exercice: nom, series: parseInt(row.querySelector('input[name="series"]').value || 0), repetitions: parseInt(row.querySelector('input[name="repetitions"]').value || 0), charge: parseFloat(row.querySelector('input[name="charge"]').value || 0) });
});
body.exercises = exercises; body.utilisateur_id = parseInt(body.utilisateur_id); body.type_id = parseInt(body.type_id);
const j = await api('add_training',{method:'POST',body});
if(j.ok){ showAlert('Séance créée'); loadSessions(body.utilisateur_id); const calcUser = document.getElementById('calc_user_select').value; if(calcUser==body.utilisateur_id) showProfile(body.utilisateur_id); e.target.reset(); document.getElementById('exercises_container').innerHTML=''; addExerciseRow(document.getElementById('exercises_container')); } else showAlert(j.error,'red');
});

// calc form
document.getElementById('form_calc').addEventListener('submit', async (e)=>{ e.preventDefault(); const fd=new FormData(e.target); const body=Object.fromEntries(fd.entries()); const selectedUser = document.getElementById('calc_user_select').value; if(selectedUser) body.user_id = parseInt(selectedUser); body.nap = parseFloat(body.nap); const j=await api('add_calc',{method:'POST',body}); if(j.ok){ showAlert('Calcul enregistré'); document.getElementById('result_calc').classList.remove('hidden'); document.getElementById('result_calc').innerText = `DEJ estimée: ${Math.round(j.dej)} kcal`; loadUsers(); } else showAlert(j.error,'red'); });


// calc user select change -> populate fields and nap
document.getElementById('calc_user_select').addEventListener('change', async (e)=>{
const uid = e.target.value; if(!uid){ document.getElementById('calc_nom').value=''; document.getElementById('calc_sexe').value='homme'; document.getElementById('calc_age').value=''; document.getElementById('calc_taille').value=''; document.getElementById('calc_poids').value=''; return; }
const user = usersCache.find(u=>u.id==uid);
if(user){ document.getElementById('calc_nom').value = user.nom; document.getElementById('calc_sexe').value = user.sexe.toLowerCase(); document.getElementById('calc_age').value = user.age; document.getElementById('calc_taille').value = user.taille; document.getElementById('calc_poids').value = user.poids; }
// set nap from last calc if available
const pr = await api('user_profile',{query:{user:uid}});
if(pr.ok && pr.data.last_dej && pr.data.last_dej.nap){ const napVal = pr.data.last_dej.nap; const select = document.getElementById('select_nap'); if(Array.from(select.options).some(o=>o.value==napVal)) select.value = napVal; }
});
document.getElementById('clear_profile_calc').addEventListener('click', ()=>{ document.getElementById('calc_user_select').value=''; document.getElementById('calc_nom').value=''; document.getElementById('calc_sexe').value='homme'; document.getElementById('calc_age').value=''; document.getElementById('calc_taille').value=''; document.getElementById('calc_poids').value=''; });


// user form
document.getElementById('form_user').addEventListener('submit', async (e)=>{ e.preventDefault(); const fd=new FormData(e.target); const body=Object.fromEntries(fd.entries()); const j=await api('add_user',{method:'POST',body}); if(j.ok){ showAlert('Utilisateur ajouté'); loadUsers(); e.target.reset(); } else showAlert(j.error,'red'); });


// close modals
document.getElementById('user_profile_modal').querySelector('#close_profile').addEventListener('click', ()=>{ const m=document.getElementById('user_profile_modal'); m.classList.add('hidden'); m.classList.remove('flex','modal-open'); });
document.getElementById('session_modal').querySelector('#close_session').addEventListener('click', ()=>{ const m=document.getElementById('session_modal'); m.classList.add('hidden'); m.classList.remove('flex','modal-open'); });
}


window.addEventListener('DOMContentLoaded', init);
</script>
</body>
</html>

