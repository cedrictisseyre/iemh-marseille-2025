<?php
// filepath: /workspace/ando-guerin/index.php
$require_conn = false;
require_once 'connexion.php';
// session pour l'auth
if (session_status() === PHP_SESSION_NONE) session_start();

// Préparer des variables par défaut
$jours = $horaires = $emploi = $profs = $eleves = [];
$db_error = false;

// Si la connexion $conn n'existe pas, on affiche une erreur plus tard
if (!isset($conn) || !($conn instanceof PDO)) {
    $db_error = true;
    error_log('Connexion PDO manquante ou invalide dans connexion.php');
} else {
    try {
        // Récupérer les jours et horaires pour l'emploi du temps
        $jours = $conn->query('SELECT * FROM jours ORDER BY id')->fetchAll();
        $horaires = $conn->query('SELECT * FROM horaires ORDER BY id')->fetchAll();

        // Récupérer l'emploi du temps complet (jointure)
        $stmt = $conn->query("SELECT et.jour_id, et.horaire_id, m.nom AS matiere, CONCAT(p.prenom, ' ', p.nom) AS professeur, s.nom AS salle
            FROM emploi_temps et
            JOIN matieres m ON et.matiere_id = m.id
            LEFT JOIN professeurs p ON et.professeur_id = p.id
            LEFT JOIN salles s ON et.salle_id = s.id");
        $emploi = [];
        foreach ($stmt as $row) {
            $emploi[$row['jour_id']][$row['horaire_id']] = $row;
        }

        // Récupérer les professeurs et leurs matières
        $profs = $conn->query("SELECT p.id, p.prenom, p.nom, GROUP_CONCAT(m.nom SEPARATOR ', ') AS matieres
            FROM professeurs p
            LEFT JOIN professeurs_matieres pm ON p.id = pm.professeur_id
            LEFT JOIN matieres m ON pm.matiere_id = m.id
            GROUP BY p.id, p.prenom, p.nom
            ORDER BY p.nom")->fetchAll();

        // Récupérer les élèves
        $eleves = $conn->query('SELECT * FROM eleves ORDER BY nom, prenom')->fetchAll();
    } catch (PDOException $e) {
        error_log('Erreur BDD (index.php) : ' . $e->getMessage());
        $db_error = true;
    }
}

// --- Gestion simple de connexion/logout ---
$login_error = '';
// Gestion de l'inscription
$register_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    if ($username === '' || $password === '' || $prenom === '' || $nom === '') {
        $register_error = 'Tous les champs sont requis.';
    } elseif ($db_error) {
        $register_error = 'Impossible d\'accéder à la base de données.';
    } else {
        try {
            // vérifier si username existe
            $stmt = $conn->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
            $stmt->execute([':u' => $username]);
            if ($stmt->fetch()) {
                $register_error = 'Nom d\'utilisateur déjà utilisé.';
            } else {
                // créer user
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $conn->prepare('INSERT INTO users (username, password) VALUES (:u, :p)');
                $ins->execute([':u' => $username, ':p' => $hash]);
                $userId = $conn->lastInsertId();
                // créer eleve et lier au user
                $ins2 = $conn->prepare('INSERT INTO eleves (prenom, nom, user_id) VALUES (:prenom, :nom, :uid)');
                $ins2->execute([':prenom' => $prenom, ':nom' => $nom, ':uid' => $userId]);
                // connexion automatique
                $_SESSION['user'] = ['id' => $userId, 'username' => $username];
                // rediriger vers la page profil
                header('Location: profil.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Erreur register: ' . $e->getMessage());
            $register_error = 'Erreur lors de la création du compte.';
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    if ($username === '' || $password === '') {
        $login_error = 'Veuillez renseigner le nom d\'utilisateur et le mot de passe.';
    } elseif ($db_error) {
        $login_error = 'Impossible de vérifier les identifiants (problème de base de données).';
    } else {
        try {
            // La table attendue : users(username, password)
            $stmt = $conn->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();
            if ($user) {
                $hash = isset($user['password']) ? $user['password'] : '';
                $ok = false;
                if ($hash !== '' && (strpos($hash, '$2y$') === 0 || strpos($hash, '$argon2') === 0)) {
                    // hashé avec password_hash
                    $ok = password_verify($password, $hash);
                } else {
                    // comparation en clair (legacy)
                    $ok = ($password === $hash);
                }
                if ($ok) {
                    // succès
                    $_SESSION['user'] = [
                        'id' => $user['id'] ?? null,
                        'username' => $user['username']
                    ];
                    // rediriger vers le profil
                    header('Location: profil.php');
                    exit;
                } else {
                    $login_error = 'Identifiants invalides.';
                }
            } else {
                $login_error = 'Utilisateur non trouvé.';
            }
        } catch (PDOException $e) {
            error_log('Erreur login: ' . $e->getMessage());
            $login_error = 'Erreur lors de la vérification des identifiants.';
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mastère IHME - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Mastère IHME</h1>
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="accueil-tab" data-bs-toggle="tab" data-bs-target="#accueil" type="button" role="tab" aria-controls="accueil" aria-selected="true">Accueil</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edt-tab" data-bs-toggle="tab" data-bs-target="#edt" type="button" role="tab" aria-controls="edt" aria-selected="false">Emploi du temps</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profs-tab" data-bs-toggle="tab" data-bs-target="#profs" type="button" role="tab" aria-controls="profs" aria-selected="false">Professeurs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="eleves-tab" data-bs-toggle="tab" data-bs-target="#eleves" type="button" role="tab" aria-controls="eleves" aria-selected="false">Élèves</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="accueil" role="tabpanel" aria-labelledby="accueil-tab">
                <p>Bienvenue sur le site du Mastère IHME !</p>

                <?php if ($db_error): ?>
                    <div class="alert alert-danger">Impossible de se connecter à la base de données. Certaines fonctionnalités peuvent être indisponibles.</div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user'])): ?>
                    <div class="alert alert-success">Connecté en tant que <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>. <a href="?action=logout">Se déconnecter</a></div>
                <?php else: ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Connexion</h5>
                                        <?php if (!empty($login_error)): ?>
                                            <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
                                        <?php endif; ?>
                                        <form method="post" action="">
                                            <input type="hidden" name="action" value="login">
                                            <div class="mb-3">
                                                <label class="form-label">Nom d'utilisateur</label>
                                                <input type="text" name="username" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Mot de passe</label>
                                                <input type="password" name="password" class="form-control" required>
                                            </div>
                                            <button class="btn btn-primary" type="submit">Se connecter</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Créer un compte étudiant</h5>
                                        <?php if (!empty($register_error)): ?>
                                            <div class="alert alert-danger"><?= htmlspecialchars($register_error) ?></div>
                                        <?php endif; ?>
                                        <form method="post" action="">
                                            <input type="hidden" name="action" value="register">
                                            <div class="mb-3">
                                                <label class="form-label">Prénom</label>
                                                <input type="text" name="prenom" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nom</label>
                                                <input type="text" name="nom" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nom d'utilisateur</label>
                                                <input type="text" name="username" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Mot de passe</label>
                                                <input type="password" name="password" class="form-control" required>
                                            </div>
                                            <button class="btn btn-success" type="submit">S'inscrire</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endif; ?>
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <?php if (file_exists(__DIR__ . '/assets/hero.jpg')): ?>
                            <img src="assets/hero.jpg" alt="Bannière Mastère IHME" class="img-fluid rounded" style="max-height:420px; object-fit:cover;">
                        <?php else: ?>
                            <div class="alert alert-secondary">
                                Image introuvable : place ton image dans <code>ando-guerin/assets/hero.jpg</code> pour l'afficher ici.<br>
                                Exemple (depuis la machine hôte) :
                                <pre class="mt-2">cp /chemin/vers/ton/image.jpg /workspace/ando-guerin/assets/hero.jpg</pre>
                                Ou glisse-colle l'image dans le dossier <code>ando-guerin/assets</code> via l'explorateur de fichiers.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="edt" role="tabpanel" aria-labelledby="edt-tab">
                <h5>Emploi du temps</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Jour / Horaire</th>
                            <?php foreach ($horaires as $horaire): ?>
                                <th><?= htmlspecialchars(substr($horaire['debut'],0,5)) ?> - <?= htmlspecialchars(substr($horaire['fin'],0,5)) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($jours as $jour): ?>
                        <tr>
                            <td><?= htmlspecialchars($jour['nom']) ?></td>
                            <?php foreach ($horaires as $horaire): ?>
                                <td>
                                <?php if (isset($emploi[$jour['id']][$horaire['id']])): 
                                    $e = $emploi[$jour['id']][$horaire['id']]; ?>
                                    <strong><?= htmlspecialchars($e['matiere']) ?></strong><br>
                                    <small><?= htmlspecialchars($e['professeur']) ?></small><br>
                                    <em><?= htmlspecialchars($e['salle']) ?></em>
                                <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="profs" role="tabpanel" aria-labelledby="profs-tab">
                <ul>
                <?php foreach ($profs as $prof): ?>
                    <li><?= htmlspecialchars($prof['prenom'] . ' ' . $prof['nom']) ?> (<?= htmlspecialchars($prof['matieres']) ?>)</li>
                <?php endforeach; ?>
                </ul>
            </div>
            <div class="tab-pane fade" id="eleves" role="tabpanel" aria-labelledby="eleves-tab">
                <ul>
                    <?php foreach ($eleves as $eleve): ?>
                        <li><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        // Synchronise les onglets Bootstrap avec le hash de l'URL
        (function(){
            const tabMap = {
                '#accueil': 'accueil-tab',
                '#edt': 'edt-tab',
                '#profs': 'profs-tab',
                '#eleves': 'eleves-tab'
            };

            function activateFromHash() {
                const id = tabMap[location.hash];
                if (!id) return;
                const btn = document.getElementById(id);
                if (btn) btn.click();
            }

            // On clique sur un onglet, met à jour le hash
            Object.values(tabMap).forEach(tabId => {
                const el = document.getElementById(tabId);
                if (!el) return;
                el.addEventListener('click', function(){
                    const pair = Object.entries(tabMap).find(([k,v]) => v === tabId);
                    if (pair) history.replaceState(null,'', pair[0]);
                });
            });

            // activation initiale
            if (location.hash) activateFromHash();
            window.addEventListener('hashchange', activateFromHash);
        })();
        </script>
</body>
</html>
