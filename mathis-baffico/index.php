<?php
// index.php
// Single-file app: DEJ, Users, Trainings+Exercises (create session with multiple exercises), Profile summary
require_once 'connexion.php';


// Helper to read JSON body
function json_body(){
$p = file_get_contents('php://input');
$d = json_decode($p, true);
return $d ?: [];
}

$action = $_GET['action'] ?? null;
if ($action){
try {
switch($action){
case 'get_refs':
$refs = [];
$refs['niveaux'] = $pdo->query('SELECT * FROM ref_niveau_activite ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$refs['types'] = $pdo->query('SELECT * FROM ref_type_seance ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'data'=>$refs]);
break;


// USERS
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


// EXERCISES (standalone library)
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
// TRAININGS + multiple exercises
case 'add_training':
$d = json_body();
// required fields
$user = (int)($d['utilisateur_id'] ?? 0);
$date = $d['date_seance'] ?? date('Y-m-d');
$type_id = (int)($d['type_id'] ?? 0);
$type_label = substr($d['type_seance'] ?? '',0,100);
$duree = (int)($d['duree'] ?? 0);
$cal = (int)($d['calories_brulees'] ?? 0);
if (!$user || !$date) throw new Exception('Utilisateur et date requis');


$pdo->beginTransaction();
$stmt = $pdo->prepare('INSERT INTO entrainements (utilisateur_id, date_seance, type_seance, duree, calories_brulees, type_id) VALUES (:user,:date,:type,:duree,:cal,:type_id)');
$stmt->execute([':user'=>$user,':date'=>$date,':type'=>$type_label,':duree'=>$duree,':cal'=>$cal,':type_id'=>$type_id]);
$eid = $pdo->lastInsertId();


// exercises array expected: [{nom_exercice, series, repetitions, charge}, ...]
$exs = $d['exercises'] ?? [];
$ins = $pdo->prepare('INSERT INTO exercices (entrainement_id, nom_exercice, series, repetitions, charge) VALUES (:eid,:nom,:series,:reps,:charge)');
foreach($exs as $ex){
$nom = substr($ex['nom_exercice'] ?? '',0,200);
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
// CALCULS
case 'add_calc':
$d = json_body();
$nom = trim($d['nom'] ?? '');
$sexe = strtolower(trim($d['sexe'] ?? ''));
$age = (int)($d['age'] ?? 0);
$taille = (float)($d['taille'] ?? 0);
$poids = (float)($d['poids'] ?? 0);
$nap = (float)($d['nap'] ?? 0);
if(!$nom || !in_array($sexe,['homme','femme'])||$age<=0) throw new Exception('Données invalides');
$sexe_aff = ($sexe==='homme')? 'Homme':'Femme';
$mb = ($sexe==='homme') ? (10*$poids)+(6.25*$taille)-(5*$age)+5 : (10*$poids)+(6.25*$taille)-(5*$age)-161;
$dej = $mb * $nap;
// get or create user
$stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE nom=:nom AND sexe=:sexe AND age=:age AND taille=:taille AND poids=:poids LIMIT 1');
$stmt->execute([':nom'=>$nom,':sexe'=>$sexe_aff,':age'=>$age,':taille'=>$taille,':poids'=>$poids]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) $uid = $row['id']; else { $i=$pdo->prepare('INSERT INTO utilisateurs (nom,sexe,age,poids,taille,date_inscription) VALUES (:nom,:sexe,:age,:poids,:taille,NOW())'); $i->execute([':nom'=>$nom,':sexe'=>$sexe_aff,':age'=>$age,':poids'=>$poids,':taille'=>$taille]); $uid=$pdo->lastInsertId(); }
$lvl = $d['niveau_activite'] ?? '';
$ins = $pdo->prepare('INSERT INTO calculs (utilisateur_id, nap, niveau_activite, metabolisme_base, dej, date_calcul) VALUES (:uid,:nap,:niv,:mb,:dej,NOW())');
$ins->execute([':uid'=>$uid,':nap'=>$nap,':niv'=>$lvl,':mb'=>$mb,':dej'=>$dej]);
echo json_encode(['ok'=>true,'dej'=>$dej,'mb'=>$mb,'user_id'=>$uid]);
break;


case 'list_calcs':
$user = (int)($_GET['user'] ?? 0);
if (!$user) throw new Exception('user id required');
$stmt = $pdo->prepare('SELECT * FROM calculs WHERE utilisateur_id=:u ORDER BY date_calcul DESC LIMIT 100');
$stmt->execute([':u'=>$user]);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
break;


// STATS
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
// PROFILE
case 'user_profile':
$user = (int)($_GET['user'] ?? 0);
if (!$user) throw new Exception('user required');
// basic info
$stmt = $pdo->prepare('SELECT id, nom, sexe, age, poids, taille, date_inscription FROM utilisateurs WHERE id=:id');
$stmt->execute([':id'=>$user]); $u = $stmt->fetch(PDO::FETCH_ASSOC);
// last DEJ
$s = $pdo->prepare('SELECT dej, date_calcul FROM calculs WHERE utilisateur_id=:u ORDER BY date_calcul DESC LIMIT 1'); $s->execute([':u'=>$user]); $last_dej = $s->fetch(PDO::FETCH_ASSOC);
// last trainings
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
// If no action -> serve frontend
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DEJ & Séances - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>.tab{cursor:pointer}.tab-active{border-bottom:3px solid #ef4444;color:#ef4444}</style>
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
</section>


<!-- SESSIONS (trainings + exercises in same page) -->
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


<button class="col-span-3 bg-blue-600 text-white p-2 rounded">Créer la séance</button>
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


<!-- Profil détaillé -->
<div id="user_profile_modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center p-4">
<div class="bg-white rounded p-4 max-w-2xl w-full">
<button id="close_profile" class="float-right">✖</button>
<div id="profile_content"></div>
</div>
</div>
</section>


</main>
<footer class="text-center py-6 text-gray-500 mt-12">© <?= date('Y') ?> DEJ Coaching</footer>


<script>
// Front-end logic
let refs = {};
function showAlert(msg,type='green'){
const el=document.getElementById('alerts'); el.innerHTML=`<div class="p-2 my-2 rounded ${type==='green'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'}">${msg}</div>`;
setTimeout(()=>el.innerHTML='',3500);
}
async function api(action, opts={}){
const url = '?action=' + action + (opts.query? '&' + new URLSearchParams(opts.query): '');
const method = opts.method || 'GET';
const headers = opts.headers || {};
let body = opts.body;
if (body && typeof body !== 'string') { body = JSON.stringify(body); headers['Content-Type'] = 'application/json'; }
const res = await fetch(url, {method, headers, body});
return res.json();
}


function populateRefs(){
const napSel = document.getElementById('select_nap'); napSel.innerHTML='';
refs.niveaux.forEach(n=>{
const val = parseFloat(n.code) || parseFloat(n.libelle) || 1.2;
const lab = n.libelle || n.code || ('NAP '+val);
napSel.innerHTML += `<option value="${val}">${lab} (NAP=${val})</option>`;
});
const typeSel = document.getElementById('session_type_id'); typeSel.innerHTML='';
refs.types.forEach(t=> typeSel.innerHTML += `<option value="${t.id}">${t.libelle}</option>`);
}


async function loadUsers(){
const j = await api('list_users'); if(!j.ok) return;
const sel = document.getElementById('session_user'); const dash = document.getElementById('dash_user'); const list = document.getElementById('list_users');
sel.innerHTML=''; dash.innerHTML=''; list.innerHTML='';
j.data.forEach(u=>{
sel.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
dash.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
list.innerHTML += `<div class="p-2 border rounded flex justify-between items-center"><div><strong class="cursor-pointer user-link" data-id="${u.id}">${u.nom}</strong><div class="text-sm text-gray-500">${u.age} ans • ${u.poids} kg • ${u.taille} cm</div></div><div><button class="del-user" data-id="${u.id}">🗑️</button></div></div>`;
});
attachUserHandlers();
}


function attachUserHandlers(){
document.querySelectorAll('.del-user').forEach(b=>b.onclick = async (e)=>{ const id=e.target.dataset.id; if(!confirm('Supprimer ?')) return; const j=await api('delete_user',{method:'POST',body:{id}}); if(j.ok){ showAlert('Supprimé'); loadUsers(); } else showAlert(j.error,'red'); });
document.querySelectorAll('.user-link').forEach(a=>a.onclick = async (e)=>{ const id = e.target.dataset.id; showProfile(id); });
}


async function loadRefs(){ const j = await api('get_refs'); if(j.ok){ refs = j.data; populateRefs(); } }

// Exercises container
function addExerciseRow(data={}){
const c = document.getElementById('exercises_container');
const idx = Date.now();
const row = document.createElement('div'); row.className='grid grid-cols-4 gap-2 items-center';
row.innerHTML = `
<input name="nom_exercice" placeholder="Exercice" value="${data.nom_exercice||''}" class="border p-2 rounded">
<input name="series" placeholder="Séries" type="number" value="${data.series||''}" class="border p-2 rounded">
<input name="repetitions" placeholder="Répétitions" type="number" value="${data.repetitions||''}" class="border p-2 rounded">
<div class="flex gap-2"><input name="charge" placeholder="Charge" type="number" step="0.1" value="${data.charge||''}" class="border p-2 rounded"><button type="button" class="remove_ex p-2 rounded bg-red-100">✖</button></div>`;
c.appendChild(row);
row.querySelector('.remove_ex').onclick = ()=> row.remove();
}


async function loadSessions(userId=null){
const url = userId? ('?action=list_trainings&user=' + userId) : '?action=list_trainings';
const res = await fetch(url); const j = await res.json();
const el = document.getElementById('list_sessions'); el.innerHTML=''; if(!j.ok) return;
j.data.forEach(s=>{
el.innerHTML += `<div class="p-2 border-b"><div class="flex justify-between"><div><strong>${s.type_lib || s.type_seance}</strong><div class="text-sm text-gray-500">${s.date_seance} • ${s.duree} min • ${s.calories_brulees || 0} kcal</div></div><div><button class="view-session" data-id="${s.id}">Voir</button></div></div></div>`;
});
document.querySelectorAll('.view-session').forEach(b=>b.onclick = async (e)=>{ const id=e.target.dataset.id; const j=await api('get_training',{query:{id}}); if(j.ok) showSessionModal(j.data); });
}


function showSessionModal(data){
const html = `<h3 class="text-lg font-bold">Séance ${data.date_seance}</h3><p>${data.type_seance} • ${data.duree} min</p><h4 class="mt-2 font-semibold">Exercices</h4>` + (data.exercises.length? `<ul class="list-disc pl-6">${data.exercises.map(ex=>`<li>${ex.nom_exercice} — ${ex.series}x${ex.repetitions} • ${ex.charge}</li>`).join('')}</ul>` : '<p class="text-sm text-gray-500">Aucun exercice enregistré</p>');
alert(html.replace(/<[^>]+>/g,'')); // simple fallback: alert with text; you can replace with modal
}
// Profile
async function showProfile(userId){
const j = await api('user_profile',{query:{user:userId}});
if(!j.ok) return showAlert(j.error,'red');
const d = j.data; const container = document.getElementById('profile_content');
let html = `<h2 class="text-xl font-bold">${d.user.nom}</h2>`;
html += `<p>${d.user.age} ans • ${d.user.poids} kg • ${d.user.taille} cm</p>`;
html += `<p class="mt-2">Dernier DEJ: ${d.last_dej? (Math.round(d.last_dej.dej)+' kcal • '+d.last_dej.date_calcul) : 'Aucun'}</p>`;
html += '<h3 class="mt-4 font-semibold">Dernières séances</h3>';
if(d.trainings.length){ html += '<ul class="list-disc pl-6">' + d.trainings.map(t=>`<li>${t.date_seance} — ${t.type_seance} • ${t.duree} min</li>`).join('') + '</ul>'; }
else html += '<p class="text-sm text-gray-500">Aucune séance</p>';
container.innerHTML = html;
const modal = document.getElementById('user_profile_modal'); modal.classList.remove('hidden'); modal.classList.add('flex');
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
loadExercisesIntoLibrary();


const first = document.getElementById('dash_user').value; if(first){ drawDejChart(first); drawVolumeChart(first); }
document.getElementById('dash_user').addEventListener('change', e=>{ drawDejChart(e.target.value); drawVolumeChart(e.target.value); });


// add exercise row button
document.getElementById('add_ex_row').addEventListener('click', ()=> addExerciseRow());
// default one row
addExerciseRow();


// session form submit
document.getElementById('form_session').addEventListener('submit', async (e)=>{
e.preventDefault(); const fd = new FormData(e.target); const body = Object.fromEntries(fd.entries());
// collect exercises
const exCont = document.getElementById('exercises_container');
const exercises = [];
exCont.querySelectorAll('div').forEach(row=>{
const nom = row.querySelector('input[name="nom_exercice"]').value.trim();
if(!nom) return;
exercises.push({ nom_exercice: nom, series: row.querySelector('input[name="series"]').value || 0, repetitions: row.querySelector('input[name="repetitions"]').value || 0, charge: row.querySelector('input[name="charge"]').value || 0 });
});
body.exercises = exercises; body.utilisateur_id = parseInt(body.utilisateur_id); body.type_id = parseInt(body.type_id);
const j = await api('add_training',{method:'POST',body}); if(j.ok){ showAlert('Séance créée'); loadSessions(body.utilisateur_id); e.target.reset(); document.getElementById('exercises_container').innerHTML=''; addExerciseRow(); } else showAlert(j.error,'red');
});


// calc form
document.getElementById('form_calc').addEventListener('submit', async (e)=>{ e.preventDefault(); const fd=new FormData(e.target); const body=Object.fromEntries(fd.entries()); body.nap = parseFloat(body.nap); const j=await api('add_calc',{method:'POST',body}); if(j.ok){ showAlert('Calcul enregistré'); document.getElementById('result_calc').classList.remove('hidden'); document.getElementById('result_calc').innerText = `DEJ estimée: ${Math.round(j.dej)} kcal`; loadUsers(); } else showAlert(j.error,'red'); });


// user form
document.getElementById('form_user').addEventListener('submit', async (e)=>{ e.preventDefault(); const fd=new FormData(e.target); const body=Object.fromEntries(fd.entries()); const j=await api('add_user',{method:'POST',body}); if(j.ok){ showAlert('Utilisateur ajouté'); loadUsers(); e.target.reset(); } else showAlert(j.error,'red'); });


document.getElementById('user_profile_modal').querySelector('#close_profile').addEventListener('click', ()=>{ const m=document.getElementById('user_profile_modal'); m.classList.add('hidden'); m.classList.remove('flex'); });
}


async function loadExercisesIntoLibrary(){ const j = await api('list_exercises'); if(!j.ok) return; /* no library UI yet, could be added */ }


window.addEventListener('DOMContentLoaded', init);
</script>
</body>
</html>
