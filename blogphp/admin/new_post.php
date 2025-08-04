<?php
session_start();
require_once '../config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    
    // Resim yükleme işlemi
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $upload_dir = '../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'uploads/' . $new_filename;
            }
        }
    }
    
    if (empty($title) || empty($content)) {
        $error = "Başlık ve içerik alanları zorunludur!";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO posts (title, content, image_path, user_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $image_path, $user_id]);
            $success = "Blog yazısı başarıyla eklendi!";
            
            // Formu temizle
            $title = $content = '';
        } catch(PDOException $e) {
            $error = "Bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Yazı Ekle - Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- TinyMCE Editör -->
    <script src="https://cdn.tiny.cloud/1/ozdrnrdijnavc22h8hhcfwrv6gqbo596jdsr69abe4ij9msd/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'link image code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image table | code',
            height: 500,
            images_upload_url: 'upload.php',
            automatic_uploads: true,
            file_picker_types: 'image',
            images_reuse_filename: true,
            language: 'tr',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@23.7.19/langs/tr.js'
        });
    </script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="panel.php">Admin Paneli</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="panel.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="new_post.php">Yeni Yazı</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Ana İçerik -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Yeni Blog Yazısı Ekle</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Başlık</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">İçerik</label>
                                <textarea class="form-control" id="content" name="content"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Kapak Görseli</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">İzin verilen formatlar: JPG, JPEG, PNG, GIF</small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Yazıyı Kaydet</button>
                                <a href="panel.php" class="btn btn-secondary">İptal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 