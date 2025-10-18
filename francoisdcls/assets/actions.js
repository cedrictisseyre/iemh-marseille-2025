// actions.js — gestion AJAX pour formulaires rapides sur la page d'accueil

async function postFormJSON(url, form)
{
    const fd = new FormData(form);
    const res = await fetch(url, { method: 'POST', body: fd });
    return res.json();
}

// Toast helper
function showToast(message, type='')
{
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div'); container.className = 'toast-container'; document.body.appendChild(container); }
    const t = document.createElement('div'); t.className = 'toast ' + (type || ''); t.textContent = message; container.appendChild(t);
    setTimeout(() => { t.style.opacity = 0; setTimeout(() => t.remove(),400); }, 4000);
}

// Ajout pilote rapide
const formPilote = document.getElementById('quick-add-pilote');
if (formPilote) {
    formPilote.addEventListener('submit', async(e) => {
        e.preventDefault();
        const btn = formPilote.querySelector('button[type=submit]');
        btn.disabled = true;
        try {
            const json = await postFormJSON(formPilote.action, formPilote);
            showToast(json.message || (json.success ? 'Ok' : 'Erreur'), json.success ? 'success' : 'error');
            if (json.success) {
                formPilote.reset();
                // refresh selects if present
                await populateQuickSelects();
                if (window.refreshStats) {
                    try {
                                      window.refreshStats(); } catch (e) {
                                      }
                }
            }
        } catch (err) {
            console.error('Erreur réseau', err);
            alert('Erreur réseau lors de l\'envoi, vérifiez le serveur');
        } finally { btn.disabled = false; }
    });
}

// Ajout ecurie rapide
const formEcurie = document.getElementById('quick-add-ecurie');
if (formEcurie) {
    formEcurie.addEventListener('submit', async(e) => {
        e.preventDefault();
        const btn = formEcurie.querySelector('button[type=submit]');
        btn.disabled = true;
        try {
            const json = await postFormJSON(formEcurie.action, formEcurie);
            showToast(json.message || (json.success ? 'Ok' : 'Erreur'), json.success ? 'success' : 'error');
            if (json.success) {
                formEcurie.reset();
                await populateQuickSelects();
                if (window.refreshStats) {
                    try {
                                      window.refreshStats(); } catch (e) {
                                      }
                }
            }
        } catch (err) {
            console.error('Erreur réseau', err);
            alert('Erreur réseau lors de l\'envoi, vérifiez le serveur');
        } finally { btn.disabled = false; }
    });
}

// Ajout participation rapide
const formPart = document.getElementById('quick-add-participation');
if (formPart) {
    formPart.addEventListener('submit', async(e) => {
        e.preventDefault();
        const btn = formPart.querySelector('button[type=submit]');
        btn.disabled = true;
        try {
            const json = await postFormJSON(formPart.action, formPart);
            showToast(json.message || (json.success ? 'Ok' : 'Erreur'), json.success ? 'success' : 'error');
            if (json.success) {
                formPart.reset();
                await populateQuickSelects();
                if (window.refreshStats) {
                    try {
                                      window.refreshStats(); } catch (e) {
                                      }
                }
            }
        } catch (err) {
            console.error('Erreur réseau', err);
            alert('Erreur réseau lors de l\'envoi, vérifiez le serveur');
        } finally { btn.disabled = false; }
    });
}

// Populate selects for participation quick form
async function fetchJSON(url)
{
    const r = await fetch(url);
    return r.json();
}

async function populateQuickSelects()
{
    const selPil = document.querySelector('#quick-add-participation select[name=pilote_id]');
    const selEcu = document.querySelector('#quick-add-participation select[name=ecurie_id]');
    if (!selPil || !selEcu) {
        return;
    }
    try {
        const base = (window.BASE_PATH || '');
        const pilotes = await fetchJSON(base + '/services/pilotes.php');
        const ecuries = await fetchJSON(base + '/services/ecuries.php');
        selPil.innerHTML = '<option value="">-- Choisir --</option>' + pilotes.map(p => ` < option value = "${p.pilote_id}" > ${p.prenom} ${p.nom} < / option > `).join('');
        selEcu.innerHTML = '<option value="">-- Choisir --</option>' + ecuries.map(e => ` < option value = "${e.ecurie_id}" > ${e.nom_ecuries} < / option > `).join('');
    } catch (err) {
        console.warn('Impossible de charger pilotes/ecuries', err);
    }
}

document.addEventListener('DOMContentLoaded', populateQuickSelects);
