<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $class = $role === 'student' ? $_POST['class'] : null;
    $subject = $role === 'teacher' ? $_POST['subject'] : null;
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate inputs
    if (empty($role) || empty($name) || empty($surname) || empty($birthdate) || empty($gender) || empty($username) || empty($password) || empty($confirmPassword)) {
        echo "Lütfen tüm alanları doldurun.";
        exit;
    }

    if ($role === 'student' && empty($class)) {
        echo "Öğrenciler için sınıf seçimi zorunludur.";
        exit;
    }

    if ($role === 'teacher' && empty($subject)) {
        echo "Öğretmenler için ders alanı zorunludur.";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "Parolalar eşleşmiyor.";
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Save to a database (example with MySQL)
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "dogruhoca";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

    if ($conn->connect_error) {
        die("Bağlantı hatası: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (role, name, surname, class, subject, birthdate, gender, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $role, $name, $surname, $class, $subject, $birthdate, $gender, $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "Kayıt başarılı!";
    } else {
        echo "Kayıt sırasında bir hata oluştu: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
