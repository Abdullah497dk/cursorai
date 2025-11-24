<?php
// Database Configuration
// HOSTINGER AYARLARI - Hostinger'dan aldığınız bilgileri buraya girin
define('DB_HOST', 'localhost');  // Genellikle localhost
define('DB_NAME', 'u988284287_Dogruhoca1');  // Hostinger database adı
define('DB_USER', 'u988284287_Dogruhoca1');  // Hostinger kullanıcı adı
define('DB_PASS', 'Dogruhoca1');  // Hostinger şifresi

// Site Configuration
define('SITE_URL', 'https://lightskyblue-cod-902795.hostingersite.com/');  // Sitenizin URL'si
define('ADMIN_USERNAMES', ['AbdullahAdrainMorsy', 'DogruHoca']);  // Admin kullanıcıları

// Timezone Configuration
date_default_timezone_set('Europe/Istanbul');  // Türkiye saat dilimi (UTC+3)

// Upload Folders
define('UPLOAD_FOLDER', __DIR__ . '/static/profile_pics');
define('DOCUMENTS_FOLDER', __DIR__ . '/static/documents');

// Create upload folders if they don't exist
if (!file_exists(UPLOAD_FOLDER)) {
    mkdir(UPLOAD_FOLDER, 0755, true);
}
if (!file_exists(DOCUMENTS_FOLDER)) {
    mkdir(DOCUMENTS_FOLDER, 0755, true);
}

// Database Connection
function getDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        // Set MySQL timezone to match PHP timezone
        $pdo->exec("SET time_zone = '+03:00'");
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Initialize Database Tables
function initDatabase() {
    $pdo = getDB();
    
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role VARCHAR(50) NOT NULL,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(255),
        surname VARCHAR(255),
        birthdate DATE,
        gender VARCHAR(20),
        class VARCHAR(10),
        subject VARCHAR(255),
        phone VARCHAR(50),
        school_number VARCHAR(100),
        school_name VARCHAR(255),
        gmail VARCHAR(255),
        profile_picture VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Videos table
    $pdo->exec("CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        youtube_url TEXT NOT NULL,
        description TEXT,
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Documents table
    $pdo->exec("CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(255) NOT NULL,
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Links table
    $pdo->exec("CREATE TABLE IF NOT EXISTS links (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        url TEXT NOT NULL,
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Site info table
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        `key` VARCHAR(100) UNIQUE NOT NULL,
        value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Olmpiyat Questions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS olmpiyat_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL,
        image_path VARCHAR(255),
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Olmpiyat Answers table
    $pdo->exec("CREATE TABLE IF NOT EXISTS olmpiyat_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        answer_text TEXT NOT NULL,
        user_id INT NOT NULL,
        user_name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (question_id) REFERENCES olmpiyat_questions(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Insert default site info
    $stmt = $pdo->prepare("INSERT IGNORE INTO site_info (`key`, value) VALUES (?, ?)");
    $stmt->execute(['about_text', 'Merhaba, Ben Doğru Hoca. [Buraya kendi özgeçmişinizi ve deneyimlerinizi ekleyebilirsiniz]']);
    $stmt->execute(['contact_email', 'dogrumehmet@gmail.com']);
    $stmt->execute(['contact_phone', '+90 505 781 07 60']);
    $stmt->execute(['contact_address', 'İstanbul, Türkiye']);
}

// Start session
session_start();
?>
