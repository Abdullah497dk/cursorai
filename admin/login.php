// Giriş başarılıysa:
if ($user && password_verify($password, $user['password'])) {
    session_start();
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Role'a göre yönlendir
    if ($user['role'] == 'admin') {
        header("Location: admin_panel.php");
    } else {
        header("Location: main_page.php");
    }
}
