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
        
        /* Progress Bar */
        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }
        
        .progress-bar .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
            transition: all 0.3s;
        }
        
        .progress-bar .step.active {
            background: #3498db;
            color: white;
        }
        
        .progress-bar .line {
            width: 100px;
            height: 3px;
            background: #ddd;
            transition: all 0.3s;
        }
        
        .progress-bar .line.active {
            background: #3498db;
        }
        
        /* Role Selection Buttons */
        #step1 {
            text-align: center;
        }
        
        #step1 h2 {
            margin-bottom: 2rem;
            color: #2c3e50;
        }
        
        #step1 button {
            width: 200px;
            padding: 1.5rem 2rem;
            margin: 0.5rem;
            font-size: 1.2rem;
            border: 2px solid #3498db;
            background: white;
            color: #3498db;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
        }
        
        #step1 button:hover {
            background: #3498db;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <header>
        <h1>dogruhoca</h1>
        <p>Eğitim ve Öğretim Portalına Hoş Geldiniz</p>
    </header>

    <div class="container">
        <?php if ($flash): ?>
        <div class="flashes">
            <ul>
                <li><?php echo htmlspecialchars($flash['message']); ?></li>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="step active" id="progress-step1">1</div>
            <div class="line" id="progress-line"></div>
            <div class="step" id="progress-step2">2</div>
        </div>

        <!-- Step 1: Role Selection -->
        <div id="step1">
            <h2>Rol Seçimi</h2>
            <button type="button" id="student-button">Öğrenci</button>
            <button type="button" id="teacher-button">Öğretmen</button>
        </div>

        <!-- Step 2: Student Form -->
        <div id="student-form" style="display: none;">
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
                <button type="button" onclick="goBack()">Geri</button>
            </form>
        </div>

        <!-- Step 2: Teacher Form -->
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
                <button type="button" onclick="goBack()">Geri</button>
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
        // Role selection
        document.getElementById('student-button').addEventListener('click', function() {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('student-form').style.display = 'block';
            document.getElementById('progress-step2').classList.add('active');
            document.getElementById('progress-line').classList.add('active');
        });

        document.getElementById('teacher-button').addEventListener('click', function() {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('teacher-form').style.display = 'block';
            document.getElementById('progress-step2').classList.add('active');
            document.getElementById('progress-line').classList.add('active');
        });

        // Go back function
        function goBack() {
            document.getElementById('step1').style.display = 'block';
            document.getElementById('student-form').style.display = 'none';
            document.getElementById('teacher-form').style.display = 'none';
            document.getElementById('progress-step2').classList.remove('active');
            document.getElementById('progress-line').classList.remove('active');
        }
    </script>
</body>
</html>
