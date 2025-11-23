<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Parse ID from URL
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;
if (preg_match('/\/api\/Olimpiyat-answers\.php\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
} elseif (preg_match('/\/api\/Olimpiyat-answers\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
}

try {
    $pdo = getDB();
    
    if ($method === 'POST') {
        // Add new answer (logged in users only)
        if (!isLoggedIn()) {
            jsonResponse(['error' => 'Giriş yapmanız gerekiyor'], 401);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Get user's full name from database
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        $userName = $user['name'] ?? $_SESSION['username'];
        
        $stmt = $pdo->prepare("INSERT INTO olimpiyat_answers (question_id, answer_text, user_id, user_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['question_id'],
            $data['answer_text'],
            $_SESSION['user_id'],
            $userName
        ]);
        
        jsonResponse(['success' => true, 'id' => $pdo->lastInsertId()]);
        
    } elseif ($method === 'DELETE') {
        // Delete answer (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        if (!$id) {
            jsonResponse(['error' => 'ID gerekli'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM olimpiyat_answers WHERE id=?");
        $stmt->execute([$id]);
        
        jsonResponse(['success' => true]);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
