
<?php
require_once __DIR__ . '/connexion.php';
if (!isset($pdo) || !$pdo) {
    echo '<div style="color:red;font-weight:bold">Erreur : la connexion à la base de données a échoué. Vérifiez les identifiants et l’accessibilité du serveur.</div>';
    exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$tabsConfig = [
    'coureurs' => [
        'label'      => 'Coureurs',
        'title'      => 'Liste des coureurs',
        'link'       => 'pages/liste_coureurs.php',
        'base_sql'   => 'SELECT id_coureur, nom, prenom, nationalite, date_naissance, club FROM coureurs_UTMB',
        'count_sql'  => 'SELECT COUNT(*) FROM coureurs_UTMB',
        'searchable' => ['nom', 'prenom', 'nationalite', 'club'],
        'order'      => 'ORDER BY nom ASC, prenom ASC',
        'columns'    => [
            ['label' => 'ID',               'key' => 'id_coureur', 'link' => false],
            ['label' => 'Nom',              'key' => 'nom',        'link' => true],
            ['label' => 'Prénom',           'key' => 'prenom',     'link' => false],
            ['label' => 'Nationalité',      'key' => 'nationalite','link' => false],
            ['label' => 'Date de naissance','key' => 'date_naissance','link' => false],
            ['label' => 'Club',             'key' => 'club',       'link' => false],
        ],
    ],
    'courses' => [
        'label'      => 'Courses',
        'title'      => 'Liste des courses',
        'link'       => 'pages/liste_courses.php',
    // alignement exact avec pages/liste_courses.php (SELECT * FROM courses) — on liste explicitement les colonnes
    'base_sql'   => 'SELECT id_course, nom_course AS nom, distance_km, denivele_m, date_course FROM courses',
        'count_sql'  => 'SELECT COUNT(*) FROM courses',
        'searchable' => ['nom_course', 'lieu'],
        'order'      => 'ORDER BY date_course DESC',
        'columns'    => [
            ['label' => 'ID',             'key' => 'id_course', 'link' => false],
            ['label' => 'Nom',            'key' => 'nom',       'link' => true],
            ['label' => 'Distance (km)',  'key' => 'distance_km','link' => false],
            ['label' => 'Dénivelé (m)',   'key' => 'denivele_m','link' => false],
            ['label' => 'Date',           'key' => 'date_course','link' => false],
        ],
    ],
    'participations' => [
        'label'      => 'Participations',
        'title'      => 'Liste des participations',
        'link'       => 'pages/liste_participations.php',
        'base_sql'   => 'SELECT id_coureur, id_course, dossard, temps_final, statut FROM participation',
        'count_sql'  => 'SELECT COUNT(*) FROM participation',
        'searchable' => ['id_coureur', 'id_course', 'temps_final', 'statut'],
        'order'      => 'ORDER BY id_course DESC',
        'columns'    => [
            ['label' => 'ID Coureur', 'key' => 'id_coureur', 'link' => true],
            ['label' => 'ID Course',  'key' => 'id_course',  'link' => true],
            ['label' => 'Dossard',    'key' => 'dossard',    'link' => false],
            ['label' => 'Temps final','key' => 'temps_final','link' => false],
            ['label' => 'Statut',     'key' => 'statut',     'link' => false],
        ],
    ],
    'points' => [
        'label'      => 'Points ITRA',
        'title'      => 'Liste des points ITRA',
        'link'       => 'pages/liste_points.php',
    // Utiliser la même requête que pages/liste_points.php pour obtenir nom/prenom du coureur
    'base_sql'   => 'SELECT p.*, c.nom, c.prenom FROM points_ITRA p JOIN coureurs_UTMB c ON p.id_coureur = c.id_coureur',
    'count_sql'  => 'SELECT COUNT(*) FROM points_ITRA p',
    // searchable doit utiliser les noms de colonnes (sans préfixe d'alias) pour fonctionner
    'searchable' => ['id_coureur', 'points', 'annee'],
    'order'      => 'ORDER BY points DESC',
        'columns'    => [
            ['label' => 'ID',         'key' => 'id_point',   'link' => false],
            ['label' => 'ID Coureur', 'key' => 'id_coureur', 'link' => true],
            ['label' => 'Année',      'key' => 'annee',      'link' => false],
            ['label' => 'Points',     'key' => 'points',     'link' => false],
            ['label' => 'Nom coureur','key' => 'nom',        'link' => false],
            ['label' => 'Prénom',     'key' => 'prenom',     'link' => false],
        ],
    ],
];

$currentTab = $_GET['tab'] ?? 'coureurs';
if (!array_key_exists($currentTab, $tabsConfig)) {
    $currentTab = 'coureurs';
}
$config = $tabsConfig[$currentTab];
$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$pageSize = 20;

function tabNav(string $active, array $tabs): void {
    echo '<nav class="tabs">';
    foreach ($tabs as $key => $data) {
        $class = $active === $key ? 'active' : '';
        $label = htmlspecialchars($data['label']);
        echo "<a href='?tab={$key}' class='{$class}'>{$label}</a> ";
    }
    echo '</nav>';
}

function buildWhereClause(array $fields, string $search): array {
    if ($search === '' || empty($fields)) {
        return ['', []];
    }
    $conditions = [];
    $params     = [];
    foreach ($fields as $idx => $field) {
        $paramKey = ":search{$idx}";
        $conditions[] = "{$field} LIKE {$paramKey}";
        $params[$paramKey] = '%' . $search . '%';
    }
    return [' WHERE ' . implode(' OR ', $conditions), $params];
}

function renderTable(array $rows, array $columns, string $tab): void {
    if (empty($rows)) {
        echo '<p>Aucun résultat ne correspond à votre recherche.</p>';
        return;
    }
    // Colonnes dynamiques + colonnes reliées
    // Si une configuration de colonnes est fournie, on l'utilise (labels + ordre). Sinon, on dérive depuis la première ligne.
    $dynamicColumns = [];
    if (!empty($columns)) {
        foreach ($columns as $col) {
            $dynamicColumns[] = ['label' => $col['label'], 'key' => $col['key'], 'link' => $col['link'] ?? false];
        }
    } else {
        $firstRow = $rows[0];
        foreach (array_keys($firstRow) as $colName) {
            $dynamicColumns[] = ['label' => $colName, 'key' => $colName, 'link' => false];
        }
    }
    // Ajout colonne liée pour courses : nombre de participations
    if ($tab === 'courses') {
        $dynamicColumns[] = ['label' => 'Nombre de participations', 'key' => '__participations', 'link' => false];
    }
    // Ajout colonne liée pour points ITRA : nom/prénom sont fournis par la jointure dans la requête
    if ($tab === 'points') {
        // nous ne créons pas de colonne liée spéciale ici car 'nom' et 'prenom' sont retournés par la requête
    }
    echo '<table><thead><tr>';
    foreach ($dynamicColumns as $col) {
        echo '<th>' . htmlspecialchars($col['label']) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($dynamicColumns as $col) {
            $value = $row[$col['key']] ?? '';
            // Ajout des données reliées
            if ($col['key'] === '__participations' && isset($row['id_course'])) {
                // Compter les participations pour cette course (colonne liée)
                $pdo2 = $GLOBALS['pdo'];
                $stmt2 = $pdo2->prepare('SELECT COUNT(*) FROM participation WHERE id_course = ?');
                $stmt2->execute([$row['id_course']]);
                $value = $stmt2->fetchColumn();
            }
            $value = htmlspecialchars((string)$value);
            echo '<td>' . $value . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function renderPagination(int $total, int $page, int $pageSize, string $tab, string $search): void {
    if ($total <= $pageSize) {
        return;
    }
    $pages = (int)ceil($total / $pageSize);
    echo '<div class="pagination">';
    for ($p = 1; $p <= $pages; $p++) {
        $params = ['tab' => $tab, 'page' => $p];
        if ($search !== '') {
            $params['search'] = $search;
        }
        $href = '?' . http_build_query($params);
        $active = $p === $page ? " class='active-page'" : '';
        echo "<a{$active} href='{$href}'>" . $p . '</a> ';
    }
    echo '</div>';
}

function exportCSV(array $rows, array $columns): void {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array_map(fn($c) => $c['label'], $columns));
    foreach ($rows as $row) {
        fputcsv($output, array_map(fn($c) => $row[$c['key']] ?? '', $columns));
    }
    fclose($output);
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    [$whereClause, $searchParams] = buildWhereClause($config['searchable'], $search);
    $dataSql  = $config['base_sql'] . $whereClause . ' ' . $config['order'];
    $stmt = $pdo->prepare($dataSql);
    foreach ($searchParams as $param => $value) {
        $stmt->bindValue($param, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    exportCSV($rows, $config['columns']);
}

[$whereClause, $searchParams] = buildWhereClause($config['searchable'], $search);

$dataSql  = $config['base_sql'] . $whereClause . ' ' . $config['order'] . ' LIMIT :limit OFFSET :offset';
$countSql = $config['count_sql'] . $whereClause;

$stmt = $pdo->prepare($dataSql);
foreach ($searchParams as $param => $value) {
    $stmt->bindValue($param, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page - 1) * $pageSize, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug temporaire : afficher la requête et un aperçu des résultats si demandé
if (isset($_GET['debug']) && $_GET['debug'] == '1' && $currentTab === 'courses') {
    echo '<pre style="background:#fff3cd;padding:12px;border:1px solid #ffeeba;margin-bottom:12px;">';
    echo "DEBUG SQL: " . htmlspecialchars($dataSql) . "\n\n";
    echo "Rows fetched: " . count($rows) . "\n";
    echo "Sample (max 3):\n";
    $sample = array_slice($rows, 0, 3);
    foreach ($sample as $r) {
        echo htmlspecialchars(var_export($r, true)) . "\n";
    }
    echo '</pre>';
}

$countStmt = $pdo->prepare($countSql);
foreach ($searchParams as $param => $value) {
    $countStmt->bindValue($param, $value, PDO::PARAM_STR);
}
$countStmt->execute();
$totalRows = (int)$countStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard UTMB</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', 'Roboto', Arial, sans-serif;
            background: linear-gradient(to top, #e0f7fa 0%, #fff 100%);
            max-width: 1100px;
            margin: 0 auto;
            padding: 0;
        }
        .mountain-header {
            width: 100%;
            height: 160px;
            background: linear-gradient(to top, #b3e5fc 0%, #fff 100%);
            position: relative;
            margin-bottom: -40px;
        }
        .mountain-svg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 160px;
        }
        h1 {
            text-align: center;
            color: #34495e;
            font-size: 2.5em;
            margin-top: 0;
            letter-spacing: 2px;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .tabs a {
            background: #388e3c;
            color: #fff;
            padding: 14px 36px;
            margin: 0 7px;
            font-size: 1.15em;
            border-radius: 12px 12px 0 0;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
            font-weight: bold;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .tabs a.active {
            background: #1565c0;
            box-shadow: 0 4px 16px rgba(44,62,80,0.12);
        }
        .content {
            background: #fff;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            margin: 0 auto;
            max-width: 1000px;
            padding: 40px 32px 32px 32px;
        }
        input[type=text]{
            padding:8px;
            width:320px;
            margin-bottom:1em;
            border-radius:6px;
            border:1px solid #b3e5fc;
            font-size:1em;
        }
        button[type=submit]{
            background:#388e3c;
            color:#fff;
            border:none;
            border-radius:6px;
            padding:8px 18px;
            font-size:1em;
            cursor:pointer;
            font-weight:bold;
            box-shadow:0 2px 8px rgba(44,62,80,0.08);
            transition:background 0.2s;
        }
        button[type=submit]:hover{
            background:#1565c0;
        }
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:1em;
            background:#f9fbe7;
            border-radius:8px;
            overflow:hidden;
            box-shadow:0 2px 8px rgba(44,62,80,0.08);
        }
        th,td{
            border:1px solid #c8e6c9;
            padding:10px;
            text-align:left;
        }
        th{
            background:#b3e5fc;
            color:#1565c0;
            font-size:1.05em;
        }
        tr:nth-child(even){background:#e0f2f1;}
        a[target="_blank"]{
            background:#1565c0;
            color:#fff;
            padding:6px 16px;
            border-radius:6px;
            text-decoration:none;
            font-size:0.95em;
            float:right;
            margin-bottom:10px;
            margin-left:10px;
            box-shadow:0 2px 8px rgba(44,62,80,0.08);
        }
        a[target="_blank"]:hover{
            background:#388e3c;
        }
        .pagination {
            margin-top: 16px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        .pagination a {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #1565c0;
            text-decoration: none;
            color: #1565c0;
        }
        .pagination a.active-page {
            background: #1565c0;
            color: #fff;
            font-weight: bold;
        }
        @media (max-width: 700px) {
            .content {
                padding: 10px 2px 10px 2px;
                max-width: 100vw;
            }
            table, th, td {
                font-size: 0.9em;
            }
            .tabs a {
                padding: 8px 10px;
                font-size: 0.95em;
            }
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;color:#1565c0;font-size:3em;margin-top:32px;letter-spacing:3px;font-family:'Montserrat',Arial,sans-serif;">UTMB - Ultra-Trail du Mont-Blanc</h1>
    <div class="mountain-header">
        <svg class="mountain-svg" viewBox="0 0 1100 160" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 160L1100 160V80L900 120L800 60L700 130L600 90L500 150L400 100L300 140L200 80L100 120L0 160Z" fill="#388e3c"/>
            <path d="M0 160L1100 160V120L900 140L800 100L700 150L600 130L500 160L400 120L300 150L200 120L100 140L0 160Z" fill="#b3e5fc"/>
        </svg>
    </div>
    <h1>Tableau de bord UTMB</h1>
    <?php tabNav($currentTab, $tabsConfig); ?>
    <div class="content">
        <form method="get" style="margin-bottom:1em;">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($currentTab) ?>">
            <input type="text" name="search" placeholder="Recherche..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Rechercher</button>
            <button type="submit" name="export" value="csv" style="margin-left:10px;">Export CSV</button>
        </form>

        <?php if (!empty($config['link'])): ?>
            <a href="<?= htmlspecialchars($config['link']) ?>" target="_blank">Voir la liste complète</a>
        <?php endif; ?>

        <h2><?= htmlspecialchars($config['title']) ?></h2>
        <?php renderTable($rows, $config['columns'], $currentTab); ?>
        <?php renderPagination($totalRows, $page, $pageSize, $currentTab, $search); ?>
    </div>
</body>
</html>
