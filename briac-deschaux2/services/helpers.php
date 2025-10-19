<?php
session_start();

// ✅ Protection CSRF
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// ✅ Fonctions pour récupérer les données
function fetch_positions(PDO $pdo): array {
    return $pdo->query("SELECT id, code, libelle FROM position ORDER BY libelle")->fetchAll();
}

function fetch_teams(PDO $pdo): array {
    return $pdo->query("SELECT id_team, nom_team, conference FROM team ORDER BY conference, nom_team")->fetchAll();
}

function fetch_players(PDO $pdo): array {
    $sql = "SELECT p.*, t.nom_team, pos.libelle AS position_name
            FROM player p
            LEFT JOIN team t ON p.id_team = t.id_team
            LEFT JOIN position pos ON p.position_id = pos.id
            ORDER BY t.nom_team, p.nom";
    return $pdo->query($sql)->fetchAll();
}
?>
