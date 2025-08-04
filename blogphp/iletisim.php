<?php
require_once 'config/db.php';
require_once 'config/auth.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi girin.';
    } else {
        // Burada e-posta gönderme işlemi yapılabilir
        // Şimdilik sadece başarı mesajı gösteriyoruz
        $success = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
        
        // Formu temizle
        $name = $email = $subject = $message = '';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim - Blog Sitesi</title>
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
        .contact-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 24px rgba(0,0,0,0.06);
            padding: 40px;
            margin-top: 40px;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .contact-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 15px;
        }
        .contact-header p {
            color: #666;
            font-size: 1.1rem;
        }
        .contact-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .contact-info h5 {
            color: #222;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .contact-item i {
            width: 40px;
            height: 40px;
            background: #ff4081;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .contact-item span {
            color: #666;
            font-size: 1rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #ff4081;
            box-shadow: 0 0 0 0.2rem rgba(255, 64, 129, 0.25);
        }
        .btn-primary {
            background: #ff4081;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
        }
        .btn-primary:hover {
            background: #e91e63;
        }
        .social-links {
            text-align: center;
            margin-top: 30px;
        }
        .social-links a {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: #ff4081;
            color: white;
            border-radius: 50%;
            margin: 0 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        .social-links a:hover {
            background: #e91e63;
            transform: translateY(-2px);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-container">
                    <div class="contact-header">
                        <h1>İletişim</h1>
                        <p>Bizimle iletişime geçin. Size yardımcı olmaktan mutluluk duyarız.</p>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-info">
                                <h5>İletişim Bilgileri</h5>
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>İstanbul, Türkiye</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <span>+90 555 123 45 67</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>info@cemozan.com</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Pazartesi - Cuma: 09:00 - 18:00</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Adınız Soyadınız *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-posta Adresiniz *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Konu *</label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Mesajınız *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Mesaj Gönder
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 