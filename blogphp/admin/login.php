<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
require_once '../config/db.php';
require_once '../config/auth.php';

// Kullanıcı zaten giriş yapmışsa yönetim paneline yönlendir
if (isAdmin()) {
    header("Location: panel.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hata ayıklama: Gönderilen bilgileri göster
    echo "<p>Submitted Username: " . htmlspecialchars($username) . "</p>";
    echo "<p>Submitted Password (raw): " . $password . "</p>"; // Ham şifreyi göster

    if (empty($username) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? AND role = 'admin'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Hata ayıklama: Veritabanından çekilen kullanıcıyı göster
            echo "<p>User from DB: ";
            print_r($user);
            echo "</p>";

            if ($user && password_verify($password, $user['password'])) {
                // Hata ayıklama: password_verify öncesi değerleri ve sonucu göster
                echo "<p>Comparing \'" . $password . "\' with \'" . $user['password'] . "\'. Result: TRUE</p>";
                // Admin girişi başarılı
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = 'admin';
                header("Location: panel.php");
                exit();
            } else {
                // Hata ayıklama: password_verify öncesi değerleri ve sonucu göster (başarısızsa)
                echo "<p>Comparing \'" . $password . "\' with \'" . $user['password'] . "\'. Result: FALSE</p>";
                $error = 'Geçersiz kullanıcı adı veya şifre.';
            }
        } catch(PDOException $e) {
            $error = 'Bir hata oluştu: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #dc3545;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-danger {
            width: 100%;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Admin Girişi</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Kullanıcı Adı</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-danger">Giriş Yap</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="../index.php" class="text-decoration-none">Ana Sayfaya Dön</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 