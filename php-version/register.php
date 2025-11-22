<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = sanitize($_POST['role'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirmPassword) {
        setFlashMessage('Parolalar uyuşmuyor!', 'error');
    } else {
        $fullname = sanitize($_POST['fullname'] ?? '');
        $gender = sanitize($_POST['gender'] ?? '');
        $class = sanitize($_POST['class'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $schoolName = sanitize($_POST['school_name'] ?? '');
        $gmail = sanitize($_POST['gmail'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("INSERT INTO users (role, username, password, name, gender, class, subject, phone, school_name, gmail) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$role, $username, $hashedPassword, $fullname, $gender, $class, $subject, $phone, $schoolName, $gmail]);
            
            setFlashMessage('Kayıt başarılı! Giriş yapabilirsiniz.', 'success');
            redirect('login.php');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                setFlashMessage('Bu kullanıcı adı zaten alınmış.', 'error');
            } else {
                setFlashMessage('Kayıt sırasında bir hata oluştu.', 'error');
            }
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
    <title>Kayıt Ol - Doğru Hoca</title>
    <link rel="stylesheet" href="static/styleforms.css">
    <link rel="icon" type="image/png" href="static/img/logo.png">
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

        <div class="role-selection">
            <button onclick="showForm('student')" id="student-btn" class="active">Öğrenci</button>
            <button onclick="showForm('teacher')" id="teacher-btn">Öğretmen</button>
        </div>

        <div id="student-form">
            <h2>Öğrenci Kayıt</h2>
            <form action="register.php" method="POST">
                <input type="hidden" name="role" value="student">
                <label for="class">Sınıf:</label>
                <select id="class" name="class" required>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
                <label for="fullname">Ad Soyad:</label>
                <input type="text" id="fullname" name="fullname" placeholder="Adınızı ve soyadınızı girin" required>
                <label for="gender">Cinsiyet:</label>
                <select id="gender" name="gender" required>
                    <option value="male">Erkek</option>
                    <option value="female">Kadın</option>
                    <option value="other">Diğer</option>
                </select>
                <label for="phone">Telefon Numarası:</label>
                <input type="tel" id="phone" name="phone" placeholder="Telefon numaranızı girin" required>
                <label for="school_name">Okul Adı:</label>
                <input type="text" id="school_name" name="school_name" placeholder="Okul adınızı girin" required>
                <label for="gmail">E-posta:</label>
                <input type="email" id="gmail" name="gmail" placeholder="E-posta adresinizi girin" required>
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" placeholder="Kullanıcı adınızı girin" required>
                <label for="password">Parola:</label>
                <input type="password" id="password" name="password" placeholder="Parolanızı girin" required>
                <label for="confirm_password">Parola (Tekrar):</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Parolanızı tekrar girin" required>
                <button type="submit">Kayıt Ol</button>
            </form>
        </div>

        <div id="teacher-form" style="display: none;">
            <h2>Öğretmen Kayıt</h2>
            <form action="register.php" method="POST">
                <input type="hidden" name="role" value="teacher">
                <label for="fullname">Ad Soyad:</label>
                <input type="text" id="fullname" name="fullname" placeholder="Adınızı ve soyadınızı girin" required>
                <label for="subject">Ders:</label>
                <input type="text" id="subject" name="subject" placeholder="Dersinizi girin" required>
                <label for="gender">Cinsiyet:</label>
                <select id="gender" name="gender" required>
                    <option value="male">Erkek</option>
                    <option value="female">Kadın</option>
                    <option value="other">Diğer</option>
                </select>
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" placeholder="Kullanıcı adınızı girin" required>
                <label for="password">Parola:</label>
                <input type="password" id="password" name="password" placeholder="Parolanızı girin" required>
                <label for="confirm_password">Parola (Tekrar):</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Parolanızı tekrar girin" required>
                <button type="submit">Kayıt Ol</button>
            </form>
        </div>

        <p style="text-align: center; margin-top: 1rem;">
            Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a>
        </p>
    </div>

    <footer>
        <p>&copy; 2024 Doğru Hoca - Tüm Hakları Saklıdır</p>
    </footer>

    <script>
        function showForm(role) {
            const studentForm = document.getElementById('student-form');
            const teacherForm = document.getElementById('teacher-form');
            const studentBtn = document.getElementById('student-btn');
            const teacherBtn = document.getElementById('teacher-btn');

            if (role === 'student') {
                studentForm.style.display = 'block';
                teacherForm.style.display = 'none';
                studentBtn.classList.add('active');
                teacherBtn.classList.remove('active');
            } else {
                studentForm.style.display = 'none';
                teacherForm.style.display = 'block';
                studentBtn.classList.remove('active');
                teacherBtn.classList.add('active');
            }
        }
    </script>
</body>
</html>
