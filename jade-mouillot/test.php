<?PHP
function read($csv){
    $file = fopen($csv, 'r');
    while (!feof($file) ) {
        $line[] = fgetcsv($file, 1024);
    }
    fclose($file);
    return $line;
}
// Définir le chemin d'accès au fichier CSV
$csv = 'ministere-charge-des-sports-datasets-2025-09-23-12-26.csv';
$csv = read($csv);
echo '<pre>';
print_r($csv);
echo '</pre>';
?>