<?php
require_once 'config/db.php';
require_once 'config/auth.php';

// Arama sorgusu
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// Sayfalama için
$posts_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Toplam yazı sayısını al
if ($search) {
    $count_stmt = $conn->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?");
    $count_stmt->execute(["%$search%", "%$search%"]);
    $total_posts = $count_stmt->fetchColumn();
} else {
    $total_posts = $conn->query("SELECT COUNT(*) FROM posts")->fetchColumn();
}
$total_pages = ceil($total_posts / $posts_per_page);

// Blog yazılarını al
if ($search) {
    $stmt = $conn->prepare("SELECT posts.*, users.username 
                           FROM posts 
                           LEFT JOIN users ON posts.user_id = users.id 
                           WHERE posts.title LIKE ? OR posts.content LIKE ?
                           ORDER BY posts.created_at DESC 
                           LIMIT ? OFFSET ?");
    $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(3, $posts_per_page, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} else {
    $stmt = $conn->prepare("SELECT posts.*, users.username 
                           FROM posts 
                           LEFT JOIN users ON posts.user_id = users.id 
                           ORDER BY posts.created_at DESC 
                           LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $posts_per_page, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
}

// Popüler ve son yazılar için örnek sorgular
$popular_posts = $conn->query("SELECT id, title, image_path FROM posts ORDER BY RAND() LIMIT 3")->fetchAll();
$recent_posts = $conn->query("SELECT id, title, created_at FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Sitesi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fb;
        }
        .navbar {
            background: #fff !important;
            border-bottom: 1px solid #eee;
        }
        .navbar-brand {
            font-family: 'Pacifico', cursive;
            color: #222 !important;
            font-size: 2rem;
        }
        .nav-link {
            color: #222 !important;
            font-weight: 500;
            margin-right: 10px;
        }
        .nav-link.active, .nav-link:hover {
            color: #ff4081 !important;
        }
        .blog-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 24px rgba(0,0,0,0.06);
            margin-bottom: 40px;
            overflow: hidden;
            transition: box-shadow 0.3s;
        }
        .blog-card:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        .blog-card-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }
        .blog-card-body {
            padding: 32px 32px 24px 32px;
        }
        .blog-card-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .blog-card-meta {
            color: #888;
            font-size: 0.95rem;
            margin-bottom: 18px;
        }
        .blog-card-text {
            color: #444;
            font-size: 1.1rem;
            margin-bottom: 18px;
        }
        .sidebar {
            position: sticky;
            top: 30px;
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
        .popular-posts img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 12px;
        }
        .popular-posts .post-title {
            font-size: 1rem;
            font-weight: 600;
            color: #222;
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
        .subscribe-box input[type="email"] {
            border-radius: 20px 0 0 20px;
            border: 1px solid #eee;
            padding: 8px 16px;
            width: 70%;
        }
        .subscribe-box button {
            border-radius: 0 20px 20px 0;
            background: #ff4081;
            color: #fff;
            border: none;
            padding: 8px 20px;
        }
        .social-icons a {
            color: #fff;
            background: #ff4081;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-size: 1.1rem;
            transition: background 0.2s;
        }
        .social-icons a:hover {
            background: #222;
        }
        .calendar-box {
            text-align: center;
        }
        .calendar-box table {
            width: 100%;
            margin: 0 auto;
        }
        .tags-box .badge {
            background: #f1f1f1;
            color: #222;
            margin: 2px;
            font-size: 0.95rem;
            border-radius: 12px;
            padding: 6px 14px;
        }
        @media (max-width: 991px) {
            .sidebar { position: static; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row">
            <!-- Ana İçerik -->
            <div class="col-lg-8">
                <!-- Yazar kutusu -->
                <div class="sidebar-widget author-box mb-4 d-lg-none">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Yazar">
                    <h6>CEM OZAN ÖZENBOY
                    </h6>
                    <p>Merhaba, Ben CEM OZAN, Bu sayfayı ben yaptım

.</p>
                    <div class="social-icons mt-2">
                        <a href="#"><i class="fab fa-vk"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-dribbble"></i></a>
                    </div>
                </div>
                <?php if (empty($posts)): ?>
                    <div class="alert alert-info">Henüz hiç blog yazısı yok.</div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <div class="blog-card mb-5">
                        <?php if ($post['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="blog-card-img" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <?php else: ?>
                            <img src="https://source.unsplash.com/random/800x400/?blog" class="blog-card-img" alt="Blog görseli">
                        <?php endif; ?>
                        <div class="blog-card-body">
                            <div class="blog-card-meta mb-2">
                                YAZAR: <?php echo htmlspecialchars($post['username'] ?? 'Anonim'); ?> &nbsp;|&nbsp; <?php echo date('d.m.Y', strtotime($post['created_at'])); ?>
                            </div>
                            <div class="blog-card-title">
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </div>
                            <div class="blog-card-text">
                                <?php echo htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 180)) . '...'; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Sayfalama -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Sayfalama">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>">&lt;</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>">&gt;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 sidebar">
                <!-- Yazar kutusu (sadece büyük ekranda) -->
                <div class="sidebar-widget author-box d-none d-lg-block">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Yazar">
                    <h6>CEM OZAN ÖZENBOY</h6>
                    <p>Merhaba, Ben CEM OZAN, Bu sayfayı ben yaptım</p>
                </div>
                <!-- Popüler yazılar -->
                <div class="sidebar-widget popular-posts">
                    <h5>POPÜLER YAZILAR</h5>
                    <?php foreach ($popular_posts as $p): ?>
                    <a href="post.php?id=<?php echo $p['id']; ?>" class="d-flex align-items-center mb-3 text-decoration-none">
                        <img src="<?php echo $p['image_path'] ? htmlspecialchars($p['image_path']) : 'https://source.unsplash.com/random/60x60/?blog'; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" />
                        <div class="ms-2">
                            <div class="post-title"><?php echo htmlspecialchars($p['title']); ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <!-- Son yazılar -->
                <div class="sidebar-widget">
                    <h5>SON YAZILAR</h5>
                    <ul class="recent-posts list-unstyled">
                        <?php foreach ($recent_posts as $r): ?>
                        <li>
                            <a href="post.php?id=<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['title']); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- Abone ol kutusu -->
                <div class="sidebar-widget subscribe-box">
                    <h5>ABONE OL</h5>
                    <form class="d-flex">
                        <input type="email" class="form-control" placeholder="E-posta adresiniz">
                        <button type="submit">Abone Ol</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
