<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dogruhoca";

// Bağlantıyı kur
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Formdan gelen verileri al
$title = $_POST['title'];
$imagePath = null;

// Görsel varsa işle
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $uploadsDir = "../uploads/";
    if (!file_exists($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $uploadsDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $imagePath = "uploads/" . $fileName; // Veritabanı için
    } else {
        echo "Görsel yüklenemedi.";
        exit;
    }
}

// Veritabanına kaydet
$stmt = $conn->prepare("INSERT INTO questions (title, image_path) VALUES (?, ?)");
$stmt->bind_param("ss", $title, $imagePath);

if ($stmt->execute()) {
    echo "Soru başarıyla eklendi.";
    echo "<br><a href='add_question.html'>Yeni soru ekle</a>";
} else {
    echo "Soru eklenirken hata oluştu: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
