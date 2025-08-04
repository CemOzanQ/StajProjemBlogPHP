<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'blog_db');

try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8'");
    
    // Test sorgusu
    $test = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($test === false) {
        die("Veritabanı bağlantısı var ama users tablosu bulunamadı!");
    }
} catch(PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?> 