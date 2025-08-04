<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcının giriş yapmış olup olmadığını kontrol et
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kullanıcının admin olup olmadığını kontrol et
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Admin yetkisi gerektiren sayfalar için kontrol
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: login.php");
        exit();
    }
}

// Menü öğelerini yetkiye göre filtrele
function getMenuItems() {
    $items = [
        [
            'url' => 'index.php',
            'text' => 'Ana Sayfa',
            'show' => true
        ],
        [
            'url' => 'admin/login.php',
            'text' => 'Yönetim Paneli',
            'show' => isAdmin()
        ]
    ];

    return array_filter($items, function($item) {
        return $item['show'];
    });
}
?> 