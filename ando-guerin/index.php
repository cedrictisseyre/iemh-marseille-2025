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

        // déterminer la semaine sélectionnée (week_start = date du lundi)
        $selected_week_start = null;
        if (isset($_GET['week_start']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['week_start'])) {
            $selected_week_start = $_GET['week_start'];
        } else {
            $d = new DateTime();
            // obtenir le lundi de la semaine courante
            $d->modify('monday this week');
            $selected_week_start = $d->format('Y-m-d');
        }

        // Récupérer l'emploi du temps pour la semaine sélectionnée (jointure)
        try {
            $stmt = $conn->prepare("SELECT et.jour_id, et.horaire_id, m.nom AS matiere, CONCAT(p.prenom, ' ', p.nom) AS professeur, s.nom AS salle
                FROM emploi_temps et
                JOIN matieres m ON et.matiere_id = m.id
                LEFT JOIN professeurs p ON et.professeur_id = p.id
                LEFT JOIN salles s ON et.salle_id = s.id
                WHERE et.week_start = :ws");
            $stmt->execute([':ws' => $selected_week_start]);
        } catch (PDOException $e) {
            // Si la colonne week_start n'existe pas ou autre erreur, retomber sur une requête sans filtre
            error_log('EDT week_start filter failed, falling back: ' . $e->getMessage());
            $stmt = $conn->query("SELECT et.jour_id, et.horaire_id, m.nom AS matiere, CONCAT(p.prenom, ' ', p.nom) AS professeur, s.nom AS salle
                FROM emploi_temps et
                JOIN matieres m ON et.matiere_id = m.id
                LEFT JOIN professeurs p ON et.professeur_id = p.id
                LEFT JOIN salles s ON et.salle_id = s.id");
            // marquer qu'aucune semaine n'est sélectionnée
            $selected_week_start = null;
        }
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
        // Récupérer les matières et salles pour le formulaire EDT
        $matieres = $conn->query('SELECT * FROM matieres ORDER BY nom')->fetchAll();
        $salles = $conn->query('SELECT * FROM salles ORDER BY nom')->fetchAll();
    } catch (PDOException $e) {
        error_log('Erreur BDD (index.php) : ' . $e->getMessage());
        $db_error = true;
    }
}

// récupérer le nom/prénom de l'élève connecté (si disponible)
$current_eleve_name = null;
if (isset($_SESSION['user']['id']) && !$db_error) {
    try {
        $stmt = $conn->prepare('SELECT prenom, nom FROM eleves WHERE user_id = :uid LIMIT 1');
        $stmt->execute([':uid' => $_SESSION['user']['id']]);
        $ce = $stmt->fetch();
        if ($ce) {
            $current_eleve_name = trim($ce['prenom'] . ' ' . $ce['nom']);
        }
    } catch (PDOException $e) {
        // ne pas bloquer l'affichage si la requête échoue
        error_log('Erreur récupération élève connecté: ' . $e->getMessage());
    }
}

// --- Gestion simple de connexion/logout ---
$login_error = '';
// Gestion de l'inscription
$register_error = '';
// Gestion ajout professeur
$prof_add_error = '';
$prof_add_success = '';
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

// Ajouter un professeur (formulaire dans onglet Professeurs)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_prof') {
    $p_prenom = isset($_POST['prof_prenom']) ? trim($_POST['prof_prenom']) : '';
    $p_nom = isset($_POST['prof_nom']) ? trim($_POST['prof_nom']) : '';
    $p_matiere = isset($_POST['prof_matiere']) ? trim($_POST['prof_matiere']) : '';
    if ($p_prenom === '' || $p_nom === '' || $p_matiere === '') {
        $prof_add_error = 'Tous les champs sont requis.';
    } elseif ($db_error) {
        $prof_add_error = 'Impossible d\'accéder à la base de données.';
    } else {
        try {
            // transactionnel : créer matière si besoin, professeur, puis lien
            $conn->beginTransaction();
            // trouver ou créer la matière
            $stmt = $conn->prepare('SELECT id FROM matieres WHERE nom = :n LIMIT 1');
            $stmt->execute([':n' => $p_matiere]);
            $m = $stmt->fetch();
            if ($m) {
                $matiere_id = $m['id'];
            } else {
                $insm = $conn->prepare('INSERT INTO matieres (nom) VALUES (:n)');
                $insm->execute([':n' => $p_matiere]);
                $matiere_id = $conn->lastInsertId();
            }

            // insérer professeur
            $insp = $conn->prepare('INSERT INTO professeurs (prenom, nom) VALUES (:prenom, :nom)');
            $insp->execute([':prenom' => $p_prenom, ':nom' => $p_nom]);
            $prof_id = $conn->lastInsertId();

            // lier professeur <-> matiere
            $link = $conn->prepare('INSERT INTO professeurs_matieres (professeur_id, matiere_id) VALUES (:pid, :mid)');
            $link->execute([':pid' => $prof_id, ':mid' => $matiere_id]);

            $conn->commit();
            $prof_add_success = 'Professeur ajouté avec succès.';

            // rafraîchir la liste des profs
            $profs = $conn->query("SELECT p.id, p.prenom, p.nom, GROUP_CONCAT(m.nom SEPARATOR ', ') AS matieres
                FROM professeurs p
                LEFT JOIN professeurs_matieres pm ON p.id = pm.professeur_id
                LEFT JOIN matieres m ON pm.matiere_id = m.id
                GROUP BY p.id, p.prenom, p.nom
                ORDER BY p.nom")->fetchAll();
        } catch (PDOException $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            error_log('Erreur add_prof: ' . $e->getMessage());
            $prof_add_error = 'Erreur lors de l\'ajout du professeur.';
        }
    }
}

// Ajouter / modifier une entrée emploi du temps
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_emploi') {
    if ($db_error) {
        // rediriger avec erreur simple
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '#edt');
        exit;
    }
    $jour_id = isset($_POST['jour_id']) ? intval($_POST['jour_id']) : 0;
    $debut = isset($_POST['debut']) ? trim($_POST['debut']) : '';
    $fin = isset($_POST['fin']) ? trim($_POST['fin']) : '';
    $prof_id = isset($_POST['prof_id']) ? intval($_POST['prof_id']) : null;
    $matiere_id = isset($_POST['matiere_id']) ? intval($_POST['matiere_id']) : null;
    $salle_nom = isset($_POST['salle_nom']) ? trim($_POST['salle_nom']) : '';
    $week_start = isset($_POST['week_start']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['week_start']) ? $_POST['week_start'] : $selected_week_start;

    try {
        $conn->beginTransaction();
        // Trouver ou créer horaire (début/fin)
        $stmt = $conn->prepare('SELECT id FROM horaires WHERE debut = :d AND fin = :f LIMIT 1');
        $stmt->execute([':d' => $debut, ':f' => $fin]);
        $h = $stmt->fetch();
        if ($h) {
            $horaire_id = $h['id'];
        } else {
            $ins = $conn->prepare('INSERT INTO horaires (debut, fin) VALUES (:d, :f)');
            $ins->execute([':d' => $debut, ':f' => $fin]);
            $horaire_id = $conn->lastInsertId();
        }

        // Si matière non fournie, erreur simple
        if (!$matiere_id) {
            // rollback et redirection
            $conn->rollBack();
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '#edt');
            exit;
        }

        // Si salle fournie en nom, trouver ou créer
        $salle_id = null;
        if ($salle_nom !== '') {
            $stmt = $conn->prepare('SELECT id FROM salles WHERE nom = :n LIMIT 1');
            $stmt->execute([':n' => $salle_nom]);
            $s = $stmt->fetch();
            if ($s) {
                $salle_id = $s['id'];
            } else {
                $ins = $conn->prepare('INSERT INTO salles (nom) VALUES (:n)');
                $ins->execute([':n' => $salle_nom]);
                $salle_id = $conn->lastInsertId();
            }
        }

        // Insérer ou remplacer l'entrée emploi_temps
        // On suppose la table emploi_temps a les colonnes : jour_id, horaire_id, matiere_id, professeur_id, salle_id
        $up = $conn->prepare('REPLACE INTO emploi_temps (week_start, jour_id, horaire_id, matiere_id, professeur_id, salle_id) VALUES (:ws, :jid, :hid, :mid, :pid, :sid)');
        $up->execute([
            ':ws' => $week_start,
            ':jid' => $jour_id,
            ':hid' => $horaire_id,
            ':mid' => $matiere_id,
            ':pid' => $prof_id ?: null,
            ':sid' => $salle_id ?: null
        ]);

        $conn->commit();
    } catch (PDOException $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        error_log('Erreur add_emploi: ' . $e->getMessage());
    }
    // retourner vers l'onglet EDT
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '#edt');
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
        <?php if ($current_eleve_name): ?>
            <p class="text-center text-muted">Connecté : <strong><?= htmlspecialchars($current_eleve_name) ?></strong></p>
        <?php endif; ?>
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
            <!-- onglet Élèves supprimé, son contenu affiché dans l'entête pour l'utilisateur connecté -->
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
                        <?php
                            // Cherche automatiquement hero.(jpg|jpeg|png|webp|gif)
                            $hero = null;
                            $exts = ['jpg','jpeg','png','webp','gif'];
                            foreach ($exts as $e) {
                                $candidate = __DIR__ . '/assets/hero.' . $e;
                                if (file_exists($candidate)) { $hero = 'assets/hero.' . $e; break; }
                            }
                        ?>
                        <?php if ($hero): ?>
                                <img src="<?= htmlspecialchars($hero) ?>" alt="Bannière Mastère IHME" class="img-fluid rounded" style="max-height:420px; object-fit:cover;">
                            <?php else: ?>
                                <div class="alert alert-secondary">
                                    Image introuvable : place ton image dans <code>ando-guerin/assets/hero.png</code> (ou hero.jpg) pour l'afficher ici.<br>
                                    Exemple (depuis la machine hôte) :
                                    <pre class="mt-2">cp ~/Downloads/hero.png /workspace/ando-guerin/assets/hero.png</pre>
                                    Ou glisse-colle l'image dans le dossier <code>ando-guerin/assets</code> via l'explorateur de fichiers.
                                </div>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="edt" role="tabpanel" aria-labelledby="edt-tab">
                <h5>Emploi du temps</h5>
                <div class="mb-2 d-flex align-items-center gap-2">
                    <form method="get" id="week-form" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Semaine (lundi)</label>
                        <input type="date" name="week_start" id="week-start" class="form-control form-control-sm" value="<?= htmlspecialchars($selected_week_start) ?>">
                        <button class="btn btn-sm btn-primary" type="submit">Afficher</button>
                    </form>
                    <button class="btn btn-sm btn-success" id="btn-add-edt">Ajouter un créneau</button>
                </div>
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
                                                                <td class="edt-cell" data-jour-id="<?= htmlspecialchars($jour['id']) ?>" data-horaire-id="<?= htmlspecialchars($horaire['id']) ?>" data-debut="<?= htmlspecialchars($horaire['debut']) ?>" data-fin="<?= htmlspecialchars($horaire['fin']) ?>">
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

                                <!-- Modal pour ajouter/éditer un créneau -->
                                <div class="modal fade" id="modalEdt" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                                                    <form method="post" id="form-edt">
                                                                        <input type="hidden" name="action" value="add_emploi">
                                                                        <input type="hidden" name="week_start" id="form-week-start" value="<?= htmlspecialchars($selected_week_start) ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ajouter / Modifier un créneau</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label">Jour</label>
                                                        <select name="jour_id" class="form-select" required>
                                                            <?php foreach ($jours as $j): ?>
                                                                <option value="<?= htmlspecialchars($j['id']) ?>"><?= htmlspecialchars($j['nom']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col">
                                                            <label class="form-label">Début</label>
                                                            <input type="time" name="debut" class="form-control" required>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label">Fin</label>
                                                            <input type="time" name="fin" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2 mt-2">
                                                        <label class="form-label">Professeur</label>
                                                        <select name="prof_id" class="form-select">
                                                            <option value="">-- aucun --</option>
                                                            <?php foreach ($profs as $p): ?>
                                                                <option value="<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Matière</label>
                                                        <select name="matiere_id" class="form-select" required>
                                                            <?php foreach ($matieres as $m): ?>
                                                                <option value="<?= htmlspecialchars($m['id']) ?>"><?= htmlspecialchars($m['nom']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Salle (libre)</label>
                                                        <input type="text" name="salle_nom" class="form-control" placeholder="Ex: B201">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
            </div>
            <div class="tab-pane fade" id="profs" role="tabpanel" aria-labelledby="profs-tab">
                <?php if (!empty($prof_add_error)): ?><div class="alert alert-danger"><?= htmlspecialchars($prof_add_error) ?></div><?php endif; ?>
                <?php if (!empty($prof_add_success)): ?><div class="alert alert-success"><?= htmlspecialchars($prof_add_success) ?></div><?php endif; ?>

                <form method="post" class="row g-2 mb-3">
                    <input type="hidden" name="action" value="add_prof">
                    <div class="col-md-3">
                        <input type="text" name="prof_prenom" class="form-control" placeholder="Prénom" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="prof_nom" class="form-control" placeholder="Nom" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="prof_matiere" class="form-control" placeholder="Matière (ex: Math)" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">Ajouter</button>
                    </div>
                </form>

                <ul>
                <?php foreach ($profs as $prof): ?>
                    <li><?= htmlspecialchars($prof['prenom'] . ' ' . $prof['nom']) ?> (<?= htmlspecialchars($prof['matieres']) ?>)</li>
                <?php endforeach; ?>
                </ul>
            </div>
            <!-- suppression de la pane Élèves -->
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        // Synchronise les onglets Bootstrap avec le hash de l'URL
        (function(){
            const tabMap = {
                '#accueil': 'accueil-tab',
                '#edt': 'edt-tab',
                '#profs': 'profs-tab'
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
        <script>
        (function(){
            // Ouvre le modal pour ajouter/éditer un créneau
            const modalEl = document.getElementById('modalEdt');
            const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
            const form = document.getElementById('form-edt');

            function openModal(prefill){
                if (!modal) return;
                // remplir form
                if (prefill) {
                    if (prefill.jour_id) form.jour_id.value = prefill.jour_id;
                    if (prefill.debut) form.debut.value = prefill.debut;
                    if (prefill.fin) form.fin.value = prefill.fin;
                }
                // remplir week_start depuis le datepicker si présent
                const wk = document.getElementById('week-start');
                const hidden = document.getElementById('form-week-start');
                if (wk && hidden) hidden.value = wk.value || hidden.value;
                modal.show();
            }

            // clic sur cellule
            document.querySelectorAll('.edt-cell').forEach(td => {
                td.style.cursor = 'pointer';
                td.addEventListener('click', function(){
                    const jour = td.getAttribute('data-jour-id');
                    const debut = td.getAttribute('data-debut');
                    const fin = td.getAttribute('data-fin');
                    openModal({jour_id: jour, debut: debut, fin: fin});
                });
            });

            // bouton ajouter
            const btnAdd = document.getElementById('btn-add-edt');
            if (btnAdd) btnAdd.addEventListener('click', function(){
                // reset form
                form.reset();
                // si présence d'un premier jour on le met
                const firstJour = form.jour_id && form.jour_id.options.length ? form.jour_id.options[0].value : null;
                if (firstJour) form.jour_id.value = firstJour;
                modal.show();
            });

            // soumission du formulaire : laisser le navigateur faire le POST (simple)
        })();
        </script>
</body>
</html>
