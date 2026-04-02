<?php

$passwords = ['', 'root', 'password', 'mysql', 'admin'];

foreach ($passwords as $pwd) {
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', $pwd);
        echo "✓ SUCCESS! MySQL root password is: " . ($pwd === '' ? '(empty/blank)' : $pwd) . "\n";
        
        // Try to create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS academicore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✓ Database 'academicore' created successfully!\n";
        
        // List databases
        $stmt = $pdo->query("SHOW DATABASES");
        echo "\nAvailable databases:\n";
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "  - {$row[0]}\n";
        }
        
        exit(0);
    } catch (PDOException $e) {
        // Try next password
        continue;
    }
}

echo "✗ Could not connect with any common password.\n";
echo "Please check your MySQL installation or use phpMyAdmin.\n";
exit(1);
