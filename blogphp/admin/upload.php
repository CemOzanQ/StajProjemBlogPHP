<?php
session_start();
require_once '../config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Oturum açmanız gerekiyor']));
}

// Resim yükleme işlemi
if (isset($_FILES['file'])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['file']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed)) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $new_filename = uniqid() . '.' . $ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
            // Başarılı yanıt
            echo json_encode([
                'location' => '../uploads/' . $new_filename
            ]);
            exit;
        }
    }
}

// Hata durumunda
echo json_encode(['error' => 'Resim yüklenemedi']);
?> 