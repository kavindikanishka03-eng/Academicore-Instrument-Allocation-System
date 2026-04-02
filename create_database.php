<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "CREATE DATABASE IF NOT EXISTS academicore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    
    echo "✓ Database 'academicore' created successfully!\n";
    
    // Show databases
    $stmt = $pdo->query("SHOW DATABASES LIKE 'academicore'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✓ Database verified: {$result['Database (academicore)']}\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
