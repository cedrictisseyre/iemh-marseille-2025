<?php
// Create a sqlite DB at francoisdcls/var/test_db.sqlite by executing schema.sql and seed.sql
$base = dirname(__DIR__);
$schemaFile = $base . '/schema.sql';
$seedFile = $base . '/seed.sql';
$dbFile = __DIR__ . '/test_db.sqlite';

try {
    if (!file_exists($schemaFile)) {
        throw new RuntimeException("Missing schema file: $schemaFile");
    }
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    $schema = file_get_contents($schemaFile);
    $seed = file_get_contents($seedFile);

    // Execute the schema and seed as a single exec (they may contain multiple statements)
    $pdo->exec($schema);
    $pdo->exec($seed);

    $counts = $pdo->query("SELECT (SELECT COUNT(*) FROM pilotes) AS pilotes,
                                     (SELECT COUNT(*) FROM ecuries) AS ecuries,
                                     (SELECT COUNT(*) FROM participations) AS participations")->fetch(PDO::FETCH_ASSOC);

    echo "Created sqlite at: $dbFile\n";
    echo "Counts: " . json_encode($counts) . "\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Error creating sqlite DB: " . $e->getMessage() . "\n");
    exit(2);
}
