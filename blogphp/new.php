<?php
require_once 'config/db.php';

// En yeni yazıları al
$stmt = $conn->query("SELECT posts.*, users.username FROM posts LEFT JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC LIMIT 10");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Yazılar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fb; }
        .navbar { background: #fff !important; border-bottom: 1px solid #eee; }
        .navbar-brand { font-family: 'Pacifico', cursive; color: #222 !important; font-size: 2rem; }
        .nav-link { color: #222 !important; font-weight: 500; margin-right: 10px; }
        .nav-link.active, .nav-link:hover { color: #ff4081 !important; }
        .page-title { font-size: 2.2rem; font-weight: 700; margin: 40px 0 30px 0; }
        .reading-list-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 24px rgba(0,0,0,0.06);
            margin-bottom: 32px;
            overflow: hidden;
            display: flex;
            align-items: stretch;
        }
        .reading-list-card .number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #ff4081;
            background: #f8f9fb;
            min-width: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reading-list-card .content {
            flex: 1;
            padding: 28px 24px;
        }
        .reading-list-card .title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .reading-list-card .meta {
            color: #888;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }
        .reading-list-card .desc {
            color: #444;
            font-size: 1.05rem;
        }
        .reading-list-card .img-box {
            min-width: 180px;
            max-width: 220px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reading-list-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 0 16px 16px 0;
        }
        @media (max-width: 767px) {
            .reading-list-card { flex-direction: column; }
            .reading-list-card .img-box { max-width: 100%; min-width: 100%; }
            .reading-list-card img { border-radius: 0 0 16px 16px; height: 180px; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>
    <div class="container">
        <div class="page-title">New</div>
        <?php $i = 1; foreach ($posts as $post): ?>
        <div class="reading-list-card">
            <div class="number"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></div>
            <div class="content">
                <div class="title">
                    <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none text-dark">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </a>
                </div>
                <div class="meta">
                    BY <?php echo htmlspecialchars($post['username'] ?? 'Anonim'); ?> &nbsp;|&nbsp; <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                </div>
                <div class="desc">
                    <?php echo htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 120)) . '...'; ?>
                </div>
            </div>
            <div class="img-box">
                <?php if ($post['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php else: ?>
                    <img src="https://source.unsplash.com/random/300x200/?blog" alt="Blog görseli">
                <?php endif; ?>
            </div>
        </div>
        <?php $i++; endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 