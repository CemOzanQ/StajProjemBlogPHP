<?php
require_once 'config/db.php';

// Yazı ID'sini kontrol et
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = (int)$_GET['id'];

// Yazıyı veritabanından al
try {
    $stmt = $conn->prepare("SELECT posts.*, users.username 
                           FROM posts 
                           LEFT JOIN users ON posts.user_id = users.id 
                           WHERE posts.id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        header("Location: index.php");
        exit();
    }
} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}

// Son yazılar (PDO hatasını düzeltiyorum)
$recent_stmt = $conn->prepare("SELECT id, title, created_at FROM posts WHERE id != ? ORDER BY created_at DESC LIMIT 5");
$recent_stmt->execute([$post_id]);
$recent_posts = $recent_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Blog Sitesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        body { background: #f8f9fb; }
        .navbar { background: #fff !important; border-bottom: 1px solid #eee; }
        .navbar-brand { font-family: 'Pacifico', cursive; color: #222 !important; font-size: 2rem; }
        .nav-link { color: #222 !important; font-weight: 500; }
        .nav-link.active, .nav-link:hover { color: #ff4081 !important; }
        .post-header {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('<?php echo $post['image_path'] ? htmlspecialchars($post['image_path']) : "https://source.unsplash.com/random/1600x600/?blog"; ?>') center/cover no-repeat;
            color: white;
            padding: 100px 0 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .post-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .post-meta {
            color: #eee;
            font-size: 1.1rem;
        }
        .post-content {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 24px rgba(0,0,0,0.06);
            padding: 40px 32px;
            font-size: 1.15rem;
            line-height: 1.8;
            color: #333;
        }
        .sidebar-widget {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 24px rgba(0,0,0,0.06);
            padding: 24px 20px;
            margin-bottom: 30px;
        }
        .sidebar-widget h5 {
            font-weight: 700;
            margin-bottom: 18px;
        }
        .author-box {
            text-align: center;
        }
        .author-box img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .author-box h6 {
            margin: 0;
            font-weight: 700;
        }
        .author-box p {
            color: #888;
            font-size: 0.95rem;
        }
        .recent-posts li {
            margin-bottom: 10px;
        }
        .recent-posts a {
            color: #222;
            text-decoration: none;
        }
        .recent-posts a:hover {
            color: #ff4081;
        }
        @media (max-width: 991px) {
            .sidebar { position: static; }
            .post-header h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <?php include 'partials/navbar.php'; ?>

    <!-- Post Header -->
    <header class="post-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-meta mt-3">
                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['username'] ?? 'Anonim'); ?></span>
                <span class="ms-3"><i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
            </div>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row">
            <!-- Yazı İçeriği -->
            <div class="col-lg-8">
                <div class="post-content mb-4">
                    <?php echo $post['content']; ?>
                </div>
            </div>
            <!-- Sidebar -->
            <div class="col-lg-4 sidebar">
                <!-- Hakkımda Widget -->
                <div class="sidebar-widget author-box">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="CEM OZAN ÖZENBOY">
                    <h6 style="margin: 16px 0 8px 0; font-weight: bold;">CEM OZAN ÖZENBOY</h6>
                    <p>Merhaba, Ben CEM OZAN, Bu sayfayı ben yaptım</p>
                </div>
                <!-- Son Yazılar Widget -->
                <div class="sidebar-widget">
                    <h5>Son Yazılar</h5>
                    <ul class="recent-posts list-unstyled">
                        <?php foreach ($recent_posts as $recent): ?>
                        <li>
                            <a href="post.php?id=<?php echo $recent['id']; ?>"><?php echo htmlspecialchars($recent['title']); ?></a>
                            <small class="text-muted d-block">
                                <?php echo date('d.m.Y', strtotime($recent['created_at'])); ?>
                            </small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 