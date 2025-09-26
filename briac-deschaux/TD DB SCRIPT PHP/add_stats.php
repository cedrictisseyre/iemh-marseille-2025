<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'database_connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_player = (int) ($_POST['id_player'] ?? 0);
    $saison = date('Y');

    // Récupération des champs (convertis en int / float)
    $yards_passe = (int) ($_POST['yards_passe'] ?? 0);
    $td_passe = (int) ($_POST['td_passe'] ?? 0);
    $interceptions = (int) ($_POST['interceptions'] ?? 0);
    $yards_course = (int) ($_POST['yards_course'] ?? 0);
    $td_course = (int) ($_POST['td_course'] ?? 0);
    $receptions = (int) ($_POST['receptions'] ?? 0);
    $yards_reception = (int) ($_POST['yards_reception'] ?? 0);
    $td_reception = (int) ($_POST['td_reception'] ?? 0);
    $plaquages = (int) ($_POST['plaquages'] ?? 0);
    $sacks = (float) ($_POST['sacks'] ?? 0);
    $interceptions_def = (int) ($_POST['interceptions_def'] ?? 0);
    $fg_reussis = (int) ($_POST['fg_reussis'] ?? 0);
    $punts = (int) ($_POST['punts'] ?? 0);

    if ($id_player) {
        $sql = "INSERT INTO stats 
            (id_player, saison, yards_passe, td_passe, interceptions, yards_course, td_course, receptions, yards_reception, td_reception, plaquages, sacks, interceptions_def, fg_reussis, punts)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_player, $saison, $yards_passe, $td_passe, $interceptions, $yards_course, $td_course, $receptions, $yards_reception, $td_reception,
            $plaquages, $sacks, $interceptions_def, $fg_reussis, $punts
        ]);
    }
}

header("Location: NFL_Stats_Analyzer.php?page=stats&added=1");
exit;

