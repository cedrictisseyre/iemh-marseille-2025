// Exemple : lecture des cours avec gestion d'erreur
function chargerClubs() {
  fetch('../services/afficher_club.php')
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
function chargerSportifs() {
  fetch('../services/afficher_sportif.php')
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

// Exemple : lecture des cours avec gestion d'erreur
function chargerDisciplines() {
  fetch('../services/afficher_discipline.php')
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

// Exemple : lecture des cours avec gestion d'erreur
function chargerCourses() {
  fetch('../services/afficher_course.php')
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