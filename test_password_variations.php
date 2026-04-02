<?php

$passwords = [
    'Mysql@26',
    'mysql@26',
    'MYSQL@26',
    'Mysql26',
    'mysql26',
    'Mysql_26',
    'mysql_26',
];

echo "Testing MySQL connection with password variations...\n\n";

foreach ($passwords as $pwd) {
    try {
        echo "Trying: $pwd ... ";
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✓ SUCCESS!\n\n";
        echo "========================================\n";
        echo "Correct Password: $pwd\n";
        echo "========================================\n\n";
        
        // Create database
        echo "Creating 'academicore' database...\n";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS academicore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✓ Database 'academicore' created!\n\n";
        
        echo "Update your .env with:\n";
        echo "DB_PASSWORD=$pwd\n";
        
        exit(0);
        
    } catch (PDOException $e) {
        echo "✗\n";
    }
}

echo "\nNone of the variations worked.\n";
echo "Please verify the exact password you set for MySQL root user.\n";
exit(1);
