<?php
require 'ando-guerin/connexion.php';
if (isset($conn) && $conn instanceof PDO) {
    echo "PDO OK\n";
} else {
    echo "PDO MISSING\n";
}
