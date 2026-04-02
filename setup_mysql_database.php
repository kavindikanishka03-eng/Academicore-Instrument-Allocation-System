<?php

try {
    echo "Connecting to MySQL...\n";
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', 'Mysql@26');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL successfully!\n\n";
    
    // Create database
    echo "Creating 'academicore' database...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS academicore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'academicore' created successfully!\n\n";
    
    // Connect to the new database
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=academicore', 'root', 'Mysql@26');
    echo "✓ Connected to 'academicore' database!\n\n";
    
    // Show all databases
    echo "Available databases:\n";
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $marker = $row[0] === 'academicore' ? ' ← YOUR PROJECT DATABASE' : '';
        echo "  • {$row[0]}$marker\n";
    }
    
    echo "\n========================================\n";
    echo "✓ MySQL Configuration Complete!\n";
    echo "========================================\n";
    echo "Database: academicore\n";
    echo "Host: 127.0.0.1:3306\n";
    echo "Username: root\n";
    echo "Password: Mysql@26\n";
    echo "========================================\n\n";
    
    echo "Next: Run migrations to create tables\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
