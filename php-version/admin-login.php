<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';

if (isAdmin()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!in_array($username, ADMIN_USERNAMES)) {
        setFlashMessage('Bu kullanıcı admin değil', 'error');
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            login($user['id'], $username, 'admin');
            setFlashMessage('Admin girişi başarılı!', 'success');
            redirect('dashboard.php');
        } else {
            setFlashMessage('Kullanıcı adı veya parola hatalı', 'error');
        }
    }
}

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - Doğru Hoca</title>
    <link rel="stylesheet" href="static/styleforms.css">
    <link rel="icon" type="image/png" href="static/img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: url('static/img/2.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .admin-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 1rem;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .container form h2 {
            margin-bottom: 0.5rem;
        }
        
        .info-text {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Doğru Hoca</h1>
        <p>Admin Yönetim Paneli</p>
    </header>

    <div class="container">
        <?php if ($flash): ?>
        <div class="flashes">
            <ul>
                <li><?php echo htmlspecialchars($flash['message']); ?></li>
            </ul>
        </div>
        <?php endif; ?>

        <form action="admin-login.php" method="POST">
            <div class="admin-badge">
                <i class="fas fa-shield-alt"></i> Admin Girişi
            </div>
            <h2>Yönetici Paneli</h2>
            <p class="info-text">Sadece yetkili admin kullanıcıları giriş yapabilir</p>
            
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" id="username" name="username" placeholder="Admin kullanıcı adınızı girin" required autofocus>
            
            <label for="password">Parola:</label>
            <input type="password" id="password" name="password" placeholder="Parolanızı girin" required>
            
            <button type="submit">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </button>
            
            <p><a href="index.php">Ana Sayfaya Dön</a></p>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Doğru Hoca - Tüm Hakları Saklıdır</p>
    </footer>
</body>
</html>
