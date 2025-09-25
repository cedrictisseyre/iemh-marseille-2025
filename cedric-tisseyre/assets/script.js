// Exemple : lecture des cours avec gestion d'erreur
function chargerCours() {
  fetch('../services/lire_cours.php')
    .then(response => {
      if (!response.ok) throw new Error('Erreur serveur : ' + response.status);
      return response.text();
    })
    .then(text => {
      try {
        const data = JSON.parse(text);
        // Afficher les cours dans la page
        console.log(data);
      } catch (e) {
        console.error('Erreur de parsing JSON :', e, text);
        alert('Erreur lors de la récupération des cours.');
      }
    })
    .catch(error => {
      console.error('Erreur fetch :', error);
      alert('Erreur serveur ou réseau.');
    });
}


// Lecture des étudiants avec gestion d'erreur
function chargerEtudiants() {
  fetch('../services/lire_etudiants.php')
    .then(response => {
      if (!response.ok) throw new Error('Erreur serveur : ' + response.status);
      return response.text();
    })
    .then(text => {
      try {
        const data = JSON.parse(text);
        // Afficher les étudiants dans la page
        console.log(data);
      } catch (e) {
        console.error('Erreur de parsing JSON :', e, text);
        alert('Erreur lors de la récupération des étudiants.');
      }
    })
    .catch(error => {
      console.error('Erreur fetch :', error);
      alert('Erreur serveur ou réseau.');
    });
}

// Exemple : ajout d'un étudiant
function ajouterEtudiant(nom) {
  fetch('../services/ajouter_etudiant.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ nom })
  })
  .then(response => response.text())
  .then(result => {
    if (result === 'ok') alert('Ajout réussi');
    else alert('Erreur');
  });
}
