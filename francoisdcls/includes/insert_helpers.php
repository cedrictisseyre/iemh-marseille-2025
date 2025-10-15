<?php

/**
 * Fonctions d'insertion réutilisables pour pilotes, ecuries, participations.
 * Chaque fonction retourne un tableau ['success'=>bool,'message'=>string,'id'=>int|null]
 */

function insert_pilote(PDO $pdo, array $data): array
{
    $prenom = trim($data['prenom'] ?? '');
    $nom = trim($data['nom'] ?? '');
    if ($prenom === '' || $nom === '') {
        return ['success' => false,'message' => 'Prénom et nom requis','id' => null];
    }
    $photo = trim($data['photo'] ?? '');
    // si aucune photo fournie, utiliser une chaîne vide pour éviter d'omettre la colonne
    $photoVal = $photo === '' ? '' : $photo;
    try {
        // essayer d'insérer avec la colonne photo si elle existe dans la base
        try {
            $stmt = $pdo->prepare('INSERT INTO pilotes (prenom, nom, photo) VALUES (?, ?, ?)');
            $stmt->execute([$prenom, $nom, $photoVal]);
        } catch (PDOException $e2) {
            // si la colonne photo n'existe pas ou requête invalide, retomber sur l'ancien schéma
            $stmt = $pdo->prepare('INSERT INTO pilotes (prenom, nom) VALUES (?, ?)');
            $stmt->execute([$prenom, $nom]);
        }
        $id = $pdo->lastInsertId();
        return ['success' => true,'message' => 'Pilote ajouté','id' => (int)$id];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Erreur base de données: ' . $e->getMessage(),
            'id' => null,
        ];
    }
}

function insert_ecurie(PDO $pdo, array $data): array
{
    // la table ecuries utilise la colonne `nom_ecuries` pour le nom
    $nom = trim($data['nom'] ?? '');
    // accept either 'pays' or legacy 'siege'
    $siege = trim($data['siege'] ?? $data['pays'] ?? '');
    if ($nom === '') {
        return ['success' => false,'message' => 'Nom requis','id' => null];
    }
    try {
        // Some DB instances may not have a `siege` column; try to insert with siege if present
        try {
            $stmt = $pdo->prepare('INSERT INTO ecuries (nom_ecuries, siege) VALUES (?, ?)');
            $stmt->execute([$nom, $siege === '' ? null : $siege]);
        } catch (PDOException $e2) {
            // if siege column not present, fall back to inserting only nom_ecuries
            $stmt = $pdo->prepare('INSERT INTO ecuries (nom_ecuries) VALUES (?)');
            $stmt->execute([$nom]);
        }
        $id = $pdo->lastInsertId();
        return ['success' => true,'message' => 'Écurie ajoutée','id' => (int)$id];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Erreur base de données: ' . $e->getMessage(),
            'id' => null,
        ];
    }
}

function insert_participation(PDO $pdo, int $pilote_id, int $ecurie_id, int $annee): array
{
    if ($pilote_id <= 0 || $ecurie_id <= 0 || $annee <= 1880) {
        return ['success' => false,'message' => 'Données invalides','id' => null];
    }
    try {
        // doublon
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM participations WHERE pilote_id = ? AND ecurie_id = ? AND annee = ?');
        $stmt->execute([$pilote_id, $ecurie_id, $annee]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false,'message' => 'Participation déjà enregistrée','id' => null];
        }
        $ins = $pdo->prepare('INSERT INTO participations (annee, pilote_id, ecurie_id) VALUES (?, ?, ?)');
        $ins->execute([$annee, $pilote_id, $ecurie_id]);
        $id = $pdo->lastInsertId();
        return ['success' => true,'message' => 'Participation ajoutée','id' => (int)$id];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Erreur base de données: ' . $e->getMessage(),
            'id' => null,
        ];
    }
}
