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
        
        // Handle multipart form data for image upload
        $questionId = $_POST['question_id'] ?? null;
        $answerText = $_POST['answer_text'] ?? '';
        $imagePath = null;
        
        if (!$questionId || !$answerText) {
            jsonResponse(['error' => 'Soru ID ve cevap metni gerekli'], 400);
        }
        
        // Get user's full name from database
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        $userName = $user['name'] ?? $_SESSION['username'];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../static/olimpiyat_images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($fileExt, $allowedExts)) {
                $fileName = uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = 'olimpiyat_images/' . $fileName;
                }
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO olimpiyat_answers (question_id, answer_text, image_path, user_id, user_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $questionId,
            $answerText,
            $imagePath,
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
        
        // Get image path before deleting
        $stmt = $pdo->prepare("SELECT image_path FROM olimpiyat_answers WHERE id=?");
        $stmt->execute([$id]);
        $answer = $stmt->fetch();
        
        // Delete image file if exists
        if ($answer && $answer['image_path']) {
            $imagePath = __DIR__ . '/../static/' . $answer['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM olimpiyat_answers WHERE id=?");
        $stmt->execute([$id]);
        
        jsonResponse(['success' => true]);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
