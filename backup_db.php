<?php
// Configuration de la base de données
$dbHost = "localhost";  // O2switch utilise souvent "localhost"
$dbUser = "gehu1211_barry";
$dbPass = "gehu1211_password";
$dbName = "gehu1211_mediatekformation";

// Chemin de sauvegarde
$backupDir = "/public_html/mediatekformation/backups/";
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Nom du fichier avec la date
$date = date("Y-m-d");
$backupFile = "{$backupDir}backup_{$dbName}_{$date}.sql.gz";

// Commande mysqldump avec compression gzip
$dumpCommand = "mysqldump -h $dbHost -u $dbUser -p'$dbPass' $dbName | gzip > $backupFile";
exec($dumpCommand, $output, $returnVar);

if ($returnVar === 0) {
    echo "✅ Sauvegarde réussie : $backupFile\n";
} else {
    echo "❌ Erreur lors de la sauvegarde !\n";
}



?>
