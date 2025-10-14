// actions.js — gestion AJAX pour formulaires rapides sur la page d'accueil

async function postFormJSON(url, form) {
  const fd = new FormData(form);
  const res = await fetch(url, { method: 'POST', body: fd });
  return res.json();
}

// Ajout pilote rapide
const formPilote = document.getElementById('quick-add-pilote');
if (formPilote) {
  formPilote.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = formPilote.querySelector('button[type=submit]');
    btn.disabled = true;
    const json = await postFormJSON(formPilote.action, formPilote);
    btn.disabled = false;
    alert(json.message || (json.success ? 'Ok' : 'Erreur'));
    if (json.success) {
      formPilote.reset();
    }
  });
}

// Ajout ecurie rapide
const formEcurie = document.getElementById('quick-add-ecurie');
if (formEcurie) {
  formEcurie.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = formEcurie.querySelector('button[type=submit]');
    btn.disabled = true;
    const json = await postFormJSON(formEcurie.action, formEcurie);
    btn.disabled = false;
    alert(json.message || (json.success ? 'Ok' : 'Erreur'));
    if (json.success) formEcurie.reset();
  });
}

// Ajout participation rapide
const formPart = document.getElementById('quick-add-participation');
if (formPart) {
  formPart.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = formPart.querySelector('button[type=submit]');
    btn.disabled = true;
    const json = await postFormJSON(formPart.action, formPart);
    btn.disabled = false;
    alert(json.message || (json.success ? 'Ok' : 'Erreur'));
    if (json.success) formPart.reset();
  });
}

// Populate selects for participation quick form
async function fetchJSON(url) {
  const r = await fetch(url);
  return r.json();
}

async function populateQuickSelects() {
  const selPil = document.querySelector('#quick-add-participation select[name=pilote_id]');
  const selEcu = document.querySelector('#quick-add-participation select[name=ecurie_id]');
  if (!selPil || !selEcu) return;
  try {
  const pilotes = await fetchJSON('/francoisdcls/services/pilotes.php');
  const ecuries = await fetchJSON('/francoisdcls/services/ecuries.php');
    selPil.innerHTML = '<option value="">-- Choisir --</option>' + pilotes.map(p=>`<option value="${p.pilote_id}">${p.prenom} ${p.nom}</option>`).join('');
    selEcu.innerHTML = '<option value="">-- Choisir --</option>' + ecuries.map(e=>`<option value="${e.ecurie_id}">${e.nom}</option>`).join('');
  } catch (err) {
    console.warn('Impossible de charger pilotes/ecuries', err);
  }
}

document.addEventListener('DOMContentLoaded', populateQuickSelects);
