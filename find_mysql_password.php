<?php

// Extended password list including MySQL 8.0 defaults
$passwords = [
    '',           // Empty
    'root',       // Common
    'password',   // Common
    'mysql',      // Common
    'admin',      // Common
    '123456',     // Common
    'Password1',  // Common
    'Root123',    // Common
    'Mysql@123',  // Common
];

echo "Testing MySQL80 connection...\n\n";

foreach ($passwords as $pwd) {
    try {
        $displayPwd = $pwd === '' ? '(empty/blank)' : $pwd;
        echo "Trying password: $displayPwd ... ";
        
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✓ SUCCESS!\n\n";
        echo "========================================\n";
        echo "MySQL Root Password: $displayPwd\n";
        echo "========================================\n\n";
        
        // Create database
        echo "Creating 'academicore' database...\n";
        $pdo->exec("CREATE DATABASE IF NOT EXISTS academicore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✓ Database 'academicore' created successfully!\n\n";
        
        // Verify database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE 'academicore'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "✓ Database verified!\n\n";
        }
        
        // Show all databases
        echo "Available databases:\n";
        $stmt = $pdo->query("SHOW DATABASES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $marker = $row[0] === 'academicore' ? ' ← NEW' : '';
            echo "  • {$row[0]}$marker\n";
        }
        
        echo "\n========================================\n";
        echo "NEXT STEP: Update your .env file with:\n";
        echo "DB_PASSWORD=$pwd\n";
        echo "========================================\n";
        
        exit(0);
        
    } catch (PDOException $e) {
        echo "✗ Failed\n";
        // Try next password
        continue;
    }
}

echo "\n========================================\n";
echo "❌ Could not connect with any common password.\n";
echo "========================================\n\n";
echo "Please try one of these methods:\n\n";
echo "1. Reset MySQL root password:\n";
echo "   - Stop MySQL80 service\n";
echo "   - Follow MySQL password reset guide\n\n";
echo "2. Check if you remember setting a password during installation\n\n";
echo "3. Use MySQL Workbench or another tool to connect\n";
echo "   (it might have saved credentials)\n\n";

exit(1);
