-- Users tablosu
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO users (username, password, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin'; 

ALTER TABLE posts DROP FOREIGN KEY posts_ibfk_1; 

CREATE TABLE users_backup AS SELECT * FROM users; 

ALTER TABLE posts ADD CONSTRAINT posts_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL; 