<?php
session_start();
require_once '../config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kullanıcı bilgilerini al
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Blog yazılarını al
$stmt = $conn->query("SELECT posts.*, users.username 
                     FROM posts 
                     LEFT JOIN users ON posts.user_id = users.id 
                     ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Admin Paneli</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Hoş geldin, <?php echo htmlspecialchars($user['username']); ?></span>
                    </li>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Blog Yazıları</h2>
                    <a href="new_post.php" class="btn btn-primary">Yeni Yazı Ekle</a>
                </div>

                <?php if (empty($posts)): ?>
                    <div class="alert alert-info">
                        Henüz hiç blog yazısı yok. İlk yazınızı eklemek için "Yeni Yazı Ekle" butonuna tıklayın.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Başlık</th>
                                    <th>Yazar</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?php echo $post['id']; ?></td>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo htmlspecialchars($post['username'] ?? 'Anonim'); ?></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">Düzenle</a>
                                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu yazıyı silmek istediğinizden emin misiniz?')">Sil</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 