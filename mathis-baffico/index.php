<?php
// index.php
// Full single-file app for DEJ, users, trainings, exercises, refs
// Requires connexion.php which should create $pdo (PDO connection)


require_once 'connexion.php';
header('Content-Type: application/json; charset=utf-8');


// Helper: read JSON body
function json_body() {
$data = json_decode(file_get_contents('php://input'), true);
return $data ?: [];
}

// Simple router for AJAX actions
$action = $_GET['action'] ?? null;
if ($action) {
try {
switch ($action) {
// --- REFERENCES ---
case 'get_refs':
$refs = [];
$refs['niveaux'] = $pdo->query('SELECT * FROM ref_niveau_activite ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$refs['types'] = $pdo->query('SELECT * FROM ref_type_seance ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok' => true, 'data' => $refs]);
break;

// --- USERS CRUD ---
case 'list_users':
$stmt = $pdo->query('SELECT id, nom, sexe, age, poids, taille, date_inscription FROM utilisateurs ORDER BY nom');
echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;


case 'add_user':
$d = json_body();
$stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, sexe, age, poids, taille, date_inscription) VALUES (:nom,:sexe,:age,:poids,:taille,NOW())');
$stmt->execute([
':nom' => $d['nom'] ?? '',
':sexe' => $d['sexe'] ?? 'Homme',
':age' => (int)($d['age'] ?? 0),
':poids' => (float)($d['poids'] ?? 0),
':taille' => (float)($d['taille'] ?? 0),
]);
echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
break;


case 'update_user':
$d = json_body();
$stmt = $pdo->prepare('UPDATE utilisateurs SET nom=:nom,sexe=:sexe,age=:age,poids=:poids,taille=:taille WHERE id=:id');
$stmt->execute([
':nom'=>$d['nom'],':sexe'=>$d['sexe'],':age'=>(int)$d['age'],':poids'=>(float)$d['poids'],':taille'=>(float)$d['taille'],':id'=>(int)$d['id']
]);
echo json_encode(['ok'=>true]);
break;
case 'delete_user':
$d = json_body();
$stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id=:id');
$stmt->execute([':id'=>(int)$d['id']]);
echo json_encode(['ok'=>true]);
break;


// --- EXERCISES CRUD ---
case 'list_exercises':
$stmt = $pdo->query('SELECT * FROM exercices ORDER BY nom_exercice');
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;


case 'add_exercise':
$d = json_body();
$stmt = $pdo->prepare('INSERT INTO exercices (entrainement_id, nom_exercice, series, repetitions, charge) VALUES (NULL,:nom,:series,:repetitions,:charge)');
$stmt->execute([':nom'=>$d['nom'],':series'=>(int)$d['series'],':repetitions'=>(int)$d['repetitions'],':charge'=>(float)$d['charge']]);
echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
break;


case 'update_exercise':
$d = json_body();
$stmt = $pdo->prepare('UPDATE exercices SET nom_exercice=:nom,series=:series,repetitions=:repetitions,charge=:charge WHERE id=:id');
$stmt->execute([':nom'=>$d['nom'],':series'=>(int)$d['series'],':repetitions'=>(int)$d['repetitions'],':charge'=>(float)$d['charge'],':id'=>(int)$d['id']]);
echo json_encode(['ok'=>true]);
break;

case 'delete_exercise':
$d = json_body();
$stmt = $pdo->prepare('DELETE FROM exercices WHERE id=:id');
$stmt->execute([':id'=>(int)$d['id']]);
echo json_encode(['ok'=>true]);
break;


// --- TRAININGS ---
case 'add_training':
$d = json_body();
$stmt = $pdo->prepare('INSERT INTO entrainements (utilisateur_id, date_seance, type_seance, duree, calories_brulees, type_id) VALUES (:user,:date,:type,:duree,:cal,:type_id)');
$stmt->execute([
':user'=>(int)$d['utilisateur_id'],
':date'=>$d['date_seance'],
':type'=>$d['type_seance'],
':duree'=>(int)$d['duree'],
':cal'=>(int)($d['calories_brulees'] ?? 0),
':type_id'=>(int)($d['type_id'] ?? 0)
]);
echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
break;


case 'list_trainings':
$user = (int)($_GET['user'] ?? 0);
if ($user) {
$stmt = $pdo->prepare('SELECT e.*, r.libelle as type_lib FROM entrainements e LEFT JOIN ref_type_seance r ON e.type_id=r.id WHERE utilisateur_id=:user ORDER BY date_seance DESC');
$stmt->execute([':user'=>$user]);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
} else {
$stmt = $pdo->query('SELECT e.*, u.nom FROM entrainements e LEFT JOIN utilisateurs u ON e.utilisateur_id=u.id ORDER BY date_seance DESC');
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
}
break;// --- CALCULS: reuse your formula ---
case 'add_calc':
$d = json_body();
$nom = trim($d['nom'] ?? '');
$sexe = strtolower(trim($d['sexe'] ?? ''));
$age = (int)($d['age'] ?? 0);
$taille = (float)($d['taille'] ?? 0);
$poids = (float)($d['poids'] ?? 0);
$nap = (float)($d['nap'] ?? 0);


if (!$nom || !in_array($sexe,['homme','femme']) || $age<=0 || $taille<=0 || $poids<=0) {
throw new Exception('Données invalides');
}


$sexe_aff = ($sexe==='homme')? 'Homme' : 'Femme';
$mb = ($sexe==='homme') ? (10*$poids) + (6.25*$taille) - (5*$age) + 5 : (10*$poids) + (6.25*$taille) - (5*$age) -161;
$dej = $mb * $nap;// get or create user
$stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE nom=:nom AND sexe=:sexe AND age=:age AND taille=:taille AND poids=:poids LIMIT 1');
$stmt->execute([':nom'=>$nom,':sexe'=>$sexe_aff,':age'=>$age,':taille'=>$taille,':poids'=>$poids]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) $uid = $row['id'];
else {
$i = $pdo->prepare('INSERT INTO utilisateurs (nom,sexe,age,poids,taille,date_inscription) VALUES (:nom,:sexe,:age,:poids,:taille,NOW())');
$i->execute([':nom'=>$nom,':sexe'=>$sexe_aff,':age'=>$age,':poids'=>$poids,':taille'=>$taille]);
$uid = $pdo->lastInsertId();
}


$stmt = $pdo->prepare('INSERT INTO calculs (utilisateur_id, nap, niveau_activite, metabolisme_base, dej, date_calcul) VALUES (:uid,:nap,:niv,:mb,:dej,NOW())');
$niv = $d['niveau_activite'] ?? '';
$stmt->execute([':uid'=>$uid,':nap'=>$nap,':niv'=>$niv,':mb'=>$mb,':dej'=>$dej]);


echo json_encode(['ok'=>true,'dej'=>$dej,'mb'=>$mb,'user_id'=>$uid]);
break;case 'list_calcs':
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
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'data'=>$rows]);
break;case 'stats_weekly_volume':
$user = (int)($_GET['user'] ?? 0);
if (!$user) throw new Exception('user id required');
$stmt = $pdo->prepare('SELECT YEARWEEK(date_seance,1) as yw, SUM(duree) as minutes FROM entrainements WHERE utilisateur_id=:u GROUP BY yw ORDER BY yw DESC LIMIT 12');
$stmt->execute([':u'=>$user]);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;


default:
echo json_encode(['ok'=>false,'error'=>'action inconnue']);
}
} catch (Exception $e) {
echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
exit;
}// If no action -> serve frontend HTML (not JSON)
header('Content-Type: text/html; charset=utf-8');
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DEJ & Entraînements - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>.tab{cursor:pointer}.tab-active{border-bottom:3px solid #ef4444;color:#ef4444}</style>
</head>
<body class="bg-gray-50 font-sans text-gray-800">
<header class="bg-gradient-to-r from-blue-700 to-red-600 text-white shadow-md">
<div class="max-w-6xl mx-auto px-4 flex justify-between items-center py-4">
<h1 class="text-2xl font-bold">🏋️ DEJ & Entraînements</h1>
<nav class="flex space-x-4">
<button class="tab tab-active px-3 py-2" data-tab="dashboard">🏠 Tableau de bord</button>
<button class="tab px-3 py-2" data-tab="calcul">💪 Calcul DEJ</button>
<button class="tab px-3 py-2" data-tab="trainings">📅 Entraînements</button>
<button class="tab px-3 py-2" data-tab="exercises">📚 Exercices</button>
<button class="tab px-3 py-2" data-tab="users">👤 Utilisateurs</button>
</nav>
</div>
</header>
<main class="max-w-6xl mx-auto p-6">
<div id="alerts"></div><!-- DASHBOARD -->
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


<!-- CALCUL FORM -->
<section id="calcul" class="tab-content hidden">
<h2 class="text-xl font-bold text-blue-700 mb-4">Calcul DEJ</h2>
<form id="form_calc" class="bg-white p-6 rounded shadow space-y-4">
<div class="grid grid-cols-3 gap-4">
<input name="nom" placeholder="Nom" class="border p-2 rounded" required>
<select name="sexe" class="border p-2 rounded" required>
<option value="homme">Homme</option>
<option value="femme">Femme</option>
</select>
<input name="age" type="number" placeholder="Âge" class="border p-2 rounded" required>
</div>
<div class="grid grid-cols-3 gap-4">
<input name="taille" type="number" placeholder="Taille (cm)" class="border p-2 rounded" required>
<input name="poids" type="number" step="0.1" placeholder="Poids (kg)" class="border p-2 rounded" required>
<select name="nap" class="border p-2 rounded" required id="select_nap"></select>
</div>
<button class="bg-gradient-to-r from-blue-600 to-red-500 text-white px-4 py-2 rounded">Calculer</button>
</form>
<div id="result_calc" class="mt-4 hidden bg-green-50 p-4 rounded"></div>
</section><!-- TRAININGS -->
<section id="trainings" class="tab-content hidden">
<h2 class="text-xl font-bold text-red-700 mb-4">Entraînements</h2>
<div class="bg-white p-4 rounded shadow space-y-4">
<form id="form_training" class="grid grid-cols-2 gap-4">
<select id="training_user" name="utilisateur_id" required></select>
<select id="training_type_id" name="type_id" required></select>
<input name="date_seance" type="date" required>
<input name="duree" type="number" placeholder="Durée (min)" required>
<input name="calories_brulees" type="number" placeholder="Calories brûlées">
<input name="type_seance" placeholder="Libellé type (optionnel)">
<button type="submit" class="col-span-2 bg-blue-600 text-white p-2 rounded">Ajouter séance</button>
</form>
</div>
<div id="list_trainings" class="mt-4"></div>
</section>


<!-- EXERCISES -->
<section id="exercises" class="tab-content hidden">
<h2 class="text-xl font-bold text-gray-800 mb-4">Exercices disponibles</h2>
<div class="bg-white p-4 rounded shadow">
<form id="form_exercise" class="grid grid-cols-4 gap-2">
<input name="nom" placeholder="Nom exercice" required class="border p-2 rounded">
<input name="series" placeholder="Séries" type="number" class="border p-2 rounded">
<input name="repetitions" placeholder="Répétitions" type="number" class="border p-2 rounded">
<input name="charge" placeholder="Charge" type="number" step="0.1" class="border p-2 rounded">
<button class="col-span-4 bg-green-600 text-white p-2 rounded">Ajouter exercice</button>
</form>
<div id="list_exercises" class="mt-4"></div>
</div>
</section><!-- USERS -->
<section id="users" class="tab-content hidden">
<h2 class="text-xl font-bold text-gray-800 mb-4">Utilisateurs</h2>
<div class="bg-white p-4 rounded shadow">
<form id="form_user" class="grid grid-cols-4 gap-2">
<input name="nom" placeholder="Nom" required class="border p-2 rounded">
<select name="sexe" class="border p-2 rounded"><option value="Homme">Homme</option><option value="Femme">Femme</option></select>
<input name="age" placeholder="Âge" type="number" class="border p-2 rounded">
<input name="poids" placeholder="Poids" type="number" step="0.1" class="border p-2 rounded">
<input name="taille" placeholder="Taille" type="number" class="border p-2 rounded">
<button class="col-span-4 bg-indigo-600 text-white p-2 rounded">Ajouter utilisateur</button>
</form>
<div id="list_users" class="mt-4"></div>
</div>
</section>


</main>
<footer class="text-center py-6 text-gray-500 mt-12">© <?= date('Y') ?> DEJ Dashboard</footer>


<script>
// Minimal front-end logic
let refs = {};
function showAlert(msg, type='green'){
const el = document.getElementById('alerts');
el.innerHTML = `<div class="p-2 my-2 rounded ${type==='green'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'}">${msg}</div>`;
setTimeout(()=>el.innerHTML='',4000);
}


function loadRefs(){
return fetch('?action=get_refs').then(r=>r.json()).then(j=>{if(j.ok){refs=j.data; populateRefSelects();}else throw j.error;});
}

function populateRefSelects(){
const selNap = document.getElementById('select_nap');
selNap.innerHTML='';
refs.niveaux.forEach(n=>{
// expecting ref_niveau_activite has columns 'code' or 'libelle' and nap maybe code contains numeric
const val = parseFloat(n.code) || parseFloat(n.libelle) || 1.2;
const label = n.libelle || n.code || ('NAP '+val);
selNap.innerHTML += `<option value="${val}">${label} (NAP=${val})</option>`;
});


const typeSel = document.getElementById('training_type_id');
typeSel.innerHTML='';
refs.types.forEach(t=>{ typeSel.innerHTML += `<option value="${t.id}">${t.libelle}</option>`; });
}// Load users into selects & lists
function loadUsers(){
return fetch('?action=list_users').then(r=>r.json()).then(j=>{
if(!j.ok) throw j.error;
const users = j.data;
const sel = document.getElementById('training_user');
const dash = document.getElementById('dash_user');
const userList = document.getElementById('list_users');
sel.innerHTML=''; dash.innerHTML=''; userList.innerHTML='';
users.forEach(u=>{
sel.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
dash.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
userList.innerHTML += `<div class="p-2 border-b flex justify-between items-center"><div><strong>${u.nom}</strong><div class="text-sm text-gray-500">${u.age} ans • ${u.poids} kg • ${u.taille} cm</div></div><div><button class="edit-user" data-id="${u.id}">✏️</button> <button class="del-user" data-id="${u.id}">🗑️</button></div></div>`;
});
attachUserListHandlers();
});
}

function attachUserListHandlers(){
document.querySelectorAll('.del-user').forEach(b=>b.addEventListener('click',e=>{
const id = e.target.dataset.id;
if(!confirm('Supprimer cet utilisateur ?')) return;
fetch('?action=delete_user',{method:'POST',body:JSON.stringify({id}),headers:{'Content-Type':'application/json'}}).then(r=>r.json()).then(j=>{if(j.ok){showAlert('Utilisateur supprimé'); loadUsers();}else showAlert(j.error,'red');});
}));
}


// Exercises
function loadExercises(){
fetch('?action=list_exercises').then(r=>r.json()).then(j=>{
if(!j.ok) return;
const el = document.getElementById('list_exercises'); el.innerHTML='';
j.data.forEach(ex=>{ el.innerHTML += `<div class="p-2 border-b flex justify-between"><div><strong>${ex.nom_exercice}</strong><div class="text-sm">${ex.series}x${ex.repetitions} • ${ex.charge}</div></div><div><button class="del-ex" data-id="${ex.id}">🗑️</button></div></div>`; });
document.querySelectorAll('.del-ex').forEach(b=>b.addEventListener('click',e=>{ const id=e.target.dataset.id; fetch('?action=delete_exercise',{method:'POST',body:JSON.stringify({id}),headers:{'Content-Type':'application/json'}}).then(r=>r.json()).then(j=>{if(j.ok) loadExercises(); else showAlert(j.error,'red');}); }));
});
}


// Trainings
function loadTrainings(userId=null){
const url = userId? `?action=list_trainings&user=${userId}` : '?action=list_trainings';
fetch(url).then(r=>r.json()).then(j=>{
if(!j.ok) return;
const el = document.getElementById('list_trainings'); el.innerHTML='';
j.data.forEach(t=>{
el.innerHTML += `<div class="p-2 border-b"><div class="flex justify-between"><div><strong>${t.type_lib || t.type_seance}</strong><div class="text-sm text-gray-500">${t.date_seance} • ${t.duree} min • ${t.calories_brulees || 0} kcal</div></div><div>${t.nom || ''}</div></div></div>`;
});
});
}

// Stats charts
let chartDej=null, chartVolume=null;
function drawDejChart(userId){
fetch(`?action=stats_dej&user=${userId}`).then(r=>r.json()).then(j=>{
if(!j.ok) return;
const labels = j.data.map(x=>x.date_calcul);
const data = j.data.map(x=>parseFloat(x.dej));
const ctx = document.getElementById('chart_dej').getContext('2d');
if(chartDej) chartDej.destroy();
chartDej = new Chart(ctx, {type:'line', data:{labels, datasets:[{label:'DEJ (kcal)', data}]}});
});
}
function drawVolumeChart(userId){
fetch(`?action=stats_weekly_volume&user=${userId}`).then(r=>r.json()).then(j=>{
if(!j.ok) return;
const labels = j.data.map(x=>x.yw);
const data = j.data.map(x=>parseInt(x.minutes));
const ctx = document.getElementById('chart_volume').getContext('2d');
if(chartVolume) chartVolume.destroy();
chartVolume = new Chart(ctx,{type:'bar',data:{labels,datasets:[{label:'Minutes / semaine',data}]}});
});
}


// Init app
function init(){
// tabs
document.querySelectorAll('.tab').forEach(t=>t.addEventListener('click',e=>{
document.querySelectorAll('.tab').forEach(x=>x.classList.remove('tab-active'));
e.target.classList.add('tab-active');
document.querySelectorAll('.tab-content').forEach(s=>s.classList.add('hidden'));
document.getElementById(e.target.dataset.tab).classList.remove('hidden');
}));Promise.all([loadRefs(), loadUsers()]).then(()=>{
loadExercises();
loadTrainings();
// draw initial dashboard for first user
const firstUser = document.getElementById('dash_user').value;
if(firstUser){ drawDejChart(firstUser); drawVolumeChart(firstUser); }


document.getElementById('dash_user').addEventListener('change', e=>{ drawDejChart(e.target.value); drawVolumeChart(e.target.value); });
});


// forms
document.getElementById('form_calc').addEventListener('submit', e=>{
e.preventDefault(); const fd=new FormData(e.target); const body = Object.fromEntries(fd.entries()); body.nap = parseFloat(body.nap);
fetch('?action=add_calc',{method:'POST',body:JSON.stringify(body),headers:{'Content-Type':'application/json'}}).then(r=>r.json()).then(j=>{
if(j.ok){ showAlert('Calcul enregistré'); document.getElementById('result_calc').classList.remove('hidden'); document.getElementById('result_calc').innerText = `DEJ estimée: ${Math.round(j.dej)} kcal`; loadUsers(); } else showAlert(j.error,'red');
});
});


document.getElementById('form_user').addEventListener('submit', e=>{
e.preventDefault(); const fd=new FormData(e.target); const body=Object.fromEntries(fd.entries()); fetch('?action=add_user',{method:'POST',body:JSON.stringify(body),headers:{'Content-Type':'application/json'}}).then(r=>r.json()).then(j=>{ if(j.ok){ showAlert('Utilisateur ajouté'); loadUsers(); e.target.reset(); } else showAlert(j.error,'red'); });
});


document.getElementById('form_exercise').addEventListener('submit', e=>{
e.preventDefault(); const fd = new FormData(e.target); const body=Object.fromEntries(fd.entries()); fetch('?action=add_exercise',{method:'POST',body:JSON.stringify({nom:body.nom,series:body.series,repetitions:body.repetitions,charge:body.charge}),headers:{'Content-Type':'application/json'}}).then(r=>r.json()).then(j=>{ if(j.ok){ showAlert('Exercice ajouté'); loadExercises(); e.target.reset(); } else showAlert(j.error,'red'); });
});document.getElementById('form_training').addEventListener('submit', e=>{
e.preventDefault(); const fd=new FormData(e.target); const body=Object.fromEntries(fd.entries()); body.utilisateur_id = parseInt(body.utilisateur_id); body.type_id = parseInt(body.type_id); fetch('?action=add_training',{method:'POST',body:JSON.stringify(body),headers:{'Content-Type':'application/json'}}).then(r=>r.json()).then(j=>{ if(j.ok){ showAlert('Séance ajoutée'); loadTrainings(body.utilisateur_id); } else showAlert(j.error,'red'); });
});


}


window.addEventListener('DOMContentLoaded', init);
</script>
</body>
</html>
