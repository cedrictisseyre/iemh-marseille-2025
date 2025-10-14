<?php
/**
 * Fonctions d'insertion réutilisables pour pilotes, ecuries, participations.
 * Chaque fonction retourne un tableau ['success'=>bool,'message'=>string,'id'=>int|null]
 */

function insert_pilote(PDO $pdo, array $data): array {
    $prenom = trim($data['prenom'] ?? '');
    $nom = trim($data['nom'] ?? '');
    // la table pilotes ne possède que (pilote_id, prenom, nom) dans la base actuelle
    if ($prenom === '' || $nom === '') return ['success'=>false,'message'=>'Prénom et nom requis','id'=>null];
    try {
        $stmt = $pdo->prepare('INSERT INTO pilotes (prenom, nom) VALUES (?, ?)');
        $stmt->execute([$prenom, $nom]);
        $id = $pdo->lastInsertId();
        return ['success'=>true,'message'=>'Pilote ajouté','id'=> (int)$id];
    } catch (PDOException $e) {
        return ['success'=>false,'message'=>'Erreur base de données: '.$e->getMessage(),'id'=>null];
    }
}

function insert_ecurie(PDO $pdo, array $data): array {
    $nom = trim($data['nom'] ?? '');
    $siege = trim($data['siege'] ?? '');
    if ($nom === '') return ['success'=>false,'message'=>'Nom requis','id'=>null];
    try {
        $stmt = $pdo->prepare('INSERT INTO ecuries (nom, siege) VALUES (?, ?)');
        $stmt->execute([$nom, $siege === '' ? null : $siege]);
        $id = $pdo->lastInsertId();
        return ['success'=>true,'message'=>'Écurie ajoutée','id'=> (int)$id];
    } catch (PDOException $e) {
        return ['success'=>false,'message'=>'Erreur base de données: '.$e->getMessage(),'id'=>null];
    }
}

function insert_participation(PDO $pdo, int $pilote_id, int $ecurie_id, int $annee): array {
    if ($pilote_id <= 0 || $ecurie_id <= 0 || $annee <= 1880) return ['success'=>false,'message'=>'Données invalides','id'=>null];
    try {
        // doublon
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM participations WHERE pilote_id = ? AND ecurie_id = ? AND annee = ?');
        $stmt->execute([$pilote_id, $ecurie_id, $annee]);
        if ($stmt->fetchColumn() > 0) return ['success'=>false,'message'=>'Participation déjà enregistrée','id'=>null];
        $ins = $pdo->prepare('INSERT INTO participations (annee, pilote_id, ecurie_id) VALUES (?, ?, ?)');
        $ins->execute([$annee, $pilote_id, $ecurie_id]);
        $id = $pdo->lastInsertId();
        return ['success'=>true,'message'=>'Participation ajoutée','id'=> (int)$id];
    } catch (PDOException $e) {
        return ['success'=>false,'message'=>'Erreur base de données: '.$e->getMessage(),'id'=>null];
    }
}
