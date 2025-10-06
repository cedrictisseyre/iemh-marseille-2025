<?php
require_once 'connexion.php';

// --- FONCTIONS ---
function getUtilisateurId($pdo, $nom, $sexe, $age, $taille, $poids) {
    // Nettoyage des inputs
    $nom = trim($nom);
    $sexe = ucfirst(strtolower(trim($sexe)));
    $age = (int)$age;
    $taille = (float)$taille;
    $poids = (float)$poids;

    // Validation minimale
    if (!$nom || !in_array($sexe, ['Homme', 'Femme']) || $age <= 0 || $taille <= 0 || $poids <= 0) {
        throw new Exception("Données utilisateur invalides.");
    }

    // Vérifie si un utilisateur avec les mêmes caractéristiques existe
    $stmt = $pdo->prepare("
        SELECT id 
        FROM utilisateurs 
        WHERE nom = :nom 
          AND sexe = :sexe 
          AND age = :age 
          AND taille = :taille 
          AND poids = :poids
        LIMIT 1
    ");
    $stmt->execute([
        'nom' => $nom,
        'sexe' => $sexe,
        'age' => $age,
        'taille' => $taille,
        'poids' => $poids
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) return $row['id'];

    // Sinon, on crée un nouvel utilisateur
    $stmt = $pdo->prepare("
        INSERT INTO utilisateurs (nom, sexe, age, taille, poids) 
        VALUES (:nom, :sexe, :age, :taille, :poids)
    ");
    $stmt->execute([
        'nom' => $nom,
        'sexe' => $sexe,
        'age' => $age,
        'taille' => $taille,
        'poids' => $poids
    ]);
    return $pdo->lastInsertId();
}

// --- FORMULAIRE ---
$resultat_enregistre = false;
$erreur_formulaire = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nom = $_POST['nom'] ?? '';
        $sexe = strtolower(trim($_POST['sexe'] ?? ''));
        $age = (int)($_POST['age'] ?? 0);
        $taille = (float)($_POST['taille'] ?? 0);
        $poids = (float)($_POST['poids'] ?? 0);
        $nap = (float)($_POST['nap'] ?? 0);

        // Vérification des valeurs
        if (!$nom || !in_array($sexe, ['homme','femme']) || $age <= 0 || $taille <= 0 || $poids <= 0 || !in_array($nap, [1.2,1.375,1.55,1.725,1.9])) {
            throw new Exception("Merci de remplir correctement tous les champs.");
        }

        $sexe_affiche = ($sexe === "homme") ? "Homme" : "Femme";

        // Calcul MB
        $mb = ($sexe === "homme")
            ? (10 * $poids) + (6.25 * $taille) - (5 * $age) + 5
            : (10 * $poids) + (6.25 * $taille) - (5 * $age) - 161;

        $niveau_activite = match ($nap) {
            1.2 => "Sédentaire",
            1.375 => "Activité légère",
            1.55 => "Activité modérée",
            1.725 => "Activité intense",
            1.9 => "Activité très intense",
            default => "Inconnu",
        };

        $dej = $mb * $nap;

        // Récupère ou crée l'utilisateur
        $utilisateur_id = getUtilisateurId($pdo, $nom, $sexe_affiche, $age, $taille, $poids);

        // Enregistrement du calcul
        $stmt = $pdo->prepare("
            INSERT INTO calculs (utilisateur_id, nap, niveau_activite, metabolisme_base, dej, date_calcul) 
            VALUES (:utilisateur_id, :nap, :niveau_activite, :mb, :dej, NOW())
        ");
        $resultat_enregistre = $stmt->execute([
            'utilisateur_id' => $utilisateur_id,
            'nap' => $nap,
            'niveau_activite' => $niveau_activite,
            'mb' => $mb,
            'dej' => $dej
        ]);

    } catch (Exception $e) {
        $erreur_formulaire = $e->getMessage();
    }
}

// --- HISTORIQUE ---
$utilisateurs = $pdo->query("SELECT id, nom FROM utilisateurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$id_user = $_GET['user'] ?? null;
$historique = [];

if ($id_user) {
    $stmt = $pdo->prepare("SELECT c.*, u.nom 
                           FROM calculs c
                           JOIN utilisateurs u ON c.utilisateur_id = u.id
                           WHERE u.id = :id
                           ORDER BY c.date_calcul DESC");
    $stmt->execute(['id' => $id_user]);
    $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calculateur DEJ & Historique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .tab-active { border-bottom: 3px solid #ef4444; color: #ef4444; }
        section { display: none; }
        section.active { display: block; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

<header class="bg-gradient-to-r from-blue-700 to-red-600 text-white shadow-md">
    <div class="max-w-5xl mx-auto px-4 flex justify-between items-center py-4">
        <h1 class="text-2xl font-bold tracking-wide">🏋️ Calculateur DEJ</h1>
        <nav class="flex space-x-6 text-lg font-semibold">
            <button id="tab-calcul" class="tab-active focus:outline-none">Calculateur</button>
            <button id="tab-historique" class="focus:outline-none">Historique</button>
        </nav>
    </div>
</header>

<main class="max-w-5xl mx-auto p-6">

    <!-- SECTION CALCULATEUR -->
    <section id="section-calcul" class="active">
        <h2 class="text-xl font-bold text-blue-700 mb-4">🧮 Calculer vos dépenses énergétiques</h2>
        <form method="post" class="bg-white shadow-lg rounded-2xl p-6 space-y-4">
            <?php if(isset($erreur_formulaire)): ?>
                <div class="bg-red-100 border-l-4 border-red-600 p-4 rounded mt-4 text-red-700">
                    ⚠ <?= htmlspecialchars($erreur_formulaire) ?>
                </div>
            <?php endif; ?>
            <div>
                <label class="font-semibold">Nom :</label>
                <input type="text" name="nom" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="font-semibold">Sexe :</label>
                <select name="sexe" class="w-full border rounded p-2" required>
                    <option value="">-- Choisissez --</option>
                    <option value="homme">Homme</option>
                    <option value="femme">Femme</option>
                </select>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="font-semibold">Âge :</label>
                    <input type="number" name="age" min="0" max="120" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="font-semibold">Taille (cm) :</label>
                    <input type="number" name="taille" min="50" max="250" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="font-semibold">Poids (kg) :</label>
                    <input type="number" name="poids" min="20" max="300" step="0.1" class="w-full border rounded p-2" required>
                </div>
            </div>
            <div>
                <label class="font-semibold">Niveau d'activité :</label>
                <select name="nap" class="w-full border rounded p-2" required>
                    <option value="">-- Choisissez --</option>
                    <option value="1.2">Sédentaire (NAP = 1.2)</option>
                    <option value="1.375">Activité légère (NAP = 1.375)</option>
                    <option value="1.55">Activité modérée (NAP = 1.55)</option>
                    <option value="1.725">Activité intense (NAP = 1.725)</option>
                    <option value="1.9">Activité très intense (NAP = 1.9)</option>
                </select>
            </div>
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90">
                Calculer 🔥
            </button>
        </form>

        <?php if (isset($dej)) : ?>
            <div class="bg-green-100 border-l-4 border-green-600 mt-6 p-4 rounded">
                <h3 class="text-lg font-bold text-green-700 mb-2">Résultat :</h3>
                <p><strong><?= htmlspecialchars($nom) ?></strong> (<?= $sexe_affiche ?>, <?= $age ?> ans)</p>
                <p><?= $taille ?> cm, <?= $poids ?> kg — <?= $niveau_activite ?> (NAP <?= $nap ?>)</p>
                <p>MB : <strong><?= round($mb, 2) ?> kcal</strong></p>
                <p>DEJ : <strong class="text-blue-700"><?= round($dej, 2) ?> kcal/jour</strong></p>
                <?php if ($resultat_enregistre): ?>
                    <p class="text-green-700 font-semibold mt-1">✅ Résultat enregistré.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- SECTION HISTORIQUE -->
    <section id="section-historique">
        <h2 class="text-xl font-bold text-red-700 mb-4">📊 Historique des calculs</h2>
        <form method="get" class="mb-4">
            <label for="user" class="font-semibold">Choisir un utilisateur :</label>
            <select name="user" id="user" class="border rounded p-2" onchange="this.form.submit()">
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($utilisateurs as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= ($id_user == $u['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($id_user && $historique): ?>
            <table class="w-full border-collapse bg-white shadow-lg rounded-xl">
                <thead class="bg-gradient-to-r from-blue-600 to-red-500 text-white">
                    <tr>
                        <th class="p-2">Date</th>
                        <th class="p-2">NAP</th>
                        <th class="p-2">Activité</th>
                        <th class="p-2">MB</th>
                        <th class="p-2">DEJ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $calc): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2"><?= $calc['date_calcul'] ?></td>
                            <td class="p-2"><?= $calc['nap'] ?></td>
                            <td class="p-2"><?= htmlspecialchars($calc['niveau_activite']) ?></td>
                            <td class="p-2"><?= round($calc['metabolisme_base'], 2) ?> kcal</td>
                            <td class="p-2 font-semibold text-blue-700"><?= round($calc['dej'], 2) ?> kcal</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($id_user): ?>
            <p class="text-gray-500">Aucun calcul enregistré pour cet utilisateur.</p>
        <?php endif; ?>
    </section>
</main>

<footer class="text-center py-6 text-gray-500 mt-12">
    <p>© <?= date('Y') ?> Calculateur DEJ - Design sportif 💪</p>
</footer>

<script>
    const tabCalcul = document.getElementById('tab-calcul');
    const tabHistorique = document.getElementById('tab-historique');
    const sectionCalcul = document.getElementById('section-calcul');
    const sectionHistorique = document.getElementById('section-historique');

    tabCalcul.addEventListener('click', () => {
        tabCalcul.classList.add('tab-active');
        tabHistorique.classList.remove('tab-active');
        sectionCalcul.classList.add('active');
        sectionHistorique.classList.remove('active');
    });

    tabHistorique.addEventListener('click', () => {
        tabHistorique.classList.add('tab-active');
        tabCalcul.classList.remove('tab-active');
        sectionHistorique.classList.add('active');
        sectionCalcul.classList.remove('active');
    });
</script>
</body>
</html>