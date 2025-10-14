<?php
require 'francoisdcls/database/bdd_formule1.php';
try{
  $cols = $pdo->query('DESCRIBE pilotes')->fetchAll(PDO::FETCH_ASSOC);
  foreach($cols as $c) echo $c['Field'] . "\n";
  $r = $pdo->query('SELECT * FROM pilotes LIMIT 1')->fetch(PDO::FETCH_ASSOC);
  var_export($r);
}catch(PDOException $e){ echo 'ERR: '.$e->getMessage()."\n"; }
