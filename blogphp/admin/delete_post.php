<?php
session_start();
require_once '../config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Yazı ID'sini kontrol et
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = (int)$_GET['id'];

try {
    // Önce yazıyı veritabanından al (resim silmek için)
    $stmt = $conn->prepare("SELECT image_path FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if ($post) {
        // Yazıyı sil
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        
        // Eğer resim varsa, dosyayı da sil
        if ($post['image_path'] && file_exists('../' . $post['image_path'])) {
            unlink('../' . $post['image_path']);
        }
        
        $_SESSION['success'] = "Blog yazısı başarıyla silindi!";
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Bir hata oluştu: " . $e->getMessage();
}

header("Location: panel.php");
exit();
?> 