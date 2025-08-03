<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo "Lütfen tüm alanları doldurun.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Geçerli bir e-posta adresi girin.";
        exit;
    }

    // Email settings
    $to = "abdallamorsii@gmail.com"; // Replace with your email address
    $subject = "Yeni İletişim Formu Mesajı";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body = "Ad: $name\n";
    $body .= "E-posta: $email\n";
    $body .= "Mesaj:\n$message\n";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        echo "Mesajınız başarıyla gönderildi.";
    } else {
        echo "Mesaj gönderilirken bir hata oluştu.";
    }
}
?>
