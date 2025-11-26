<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        setFlashMessage('Lütfen tüm alanları doldurun', 'error');
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            login($user['id'], $user['username'], $user['role']);
            redirect('index.php');
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
    <title>Giriş Yap - Doğru Hoca</title>
    <link rel="stylesheet" href="static/styleforms.css">
    <link rel="icon" type="image/png" href="static/img/logo.png">
    <style>
        body {
            background-image: url('static/img/2.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: scroll; /* Mobile performance */
        }
        @media (min-width: 769px) {
            body {
                background-attachment: fixed;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Doğru Hoca</h1>
        <p>Eğitim ve Öğretim Portalı</p>
    </header>

    <div class="container">
        <?php if ($flash): ?>
        <div class="flashes">
            <ul>
                <li><?php echo htmlspecialchars($flash['message']); ?></li>
            </ul>
        </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <h2>Giriş Yap</h2>
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" id="username" name="username" required autofocus>
            <label for="password">Parola:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Giriş Yap</button>
            <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
            <p><a href="index.php">Ana Sayfaya Dön</a></p>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Doğru Hoca - Tüm Hakları Saklıdır</p>
    </footer>
</body>
</html>
