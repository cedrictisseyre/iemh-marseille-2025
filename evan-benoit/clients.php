<?php include 'header.html'; ?>

<section class="section bg-white">
  <div class="container">
    <h2>👥 Gestion des Clients</h2>
    <p class="text-center mb-5">
      Consulte, ajoute ou modifie les informations de tes athlètes. Chaque fiche client regroupe les données de progression, les séances et le suivi de masse.
    </p>

    <div class="card card-custom p-4 mb-5">
      <h4 class="mb-3">Liste des clients</h4>
      <!-- Exemple de tableau -->
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Âge</th>
            <th>Objectif</th>
            <th>Dernière mise à jour</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Dupont</td>
            <td>Lucas</td>
            <td>27</td>
            <td>Prise de masse</td>
            <td>03/10/2025</td>
            <td>
              <button class="btn btn-sm btn-outline-primary">Voir</button>
              <button class="btn btn-sm btn-outline-danger">Supprimer</button>
            </td>
          </tr>
          <tr>
            <td>Martin</td>
            <td>Emma</td>
            <td>31</td>
            <td>Sèche</td>
            <td>08/10/2025</td>
            <td>
              <button class="btn btn-sm btn-outline-primary">Voir</button>
              <button class="btn btn-sm btn-outline-danger">Supprimer</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-center">
      <button class="btn btn-primary px-4 py-2">➕ Ajouter un client</button>
    </div>
  </div>
</section>

<?php include 'footer.html'; ?>
