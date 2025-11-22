<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Parse ID from URL
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;
if (preg_match('/\/api\/olmpiyat-questions\.php\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
} elseif (preg_match('/\/api\/olmpiyat-questions\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
}

try {
    $pdo = getDB();
    
    if ($method === 'GET') {
        // Get all questions with their answers
        $stmt = $pdo->query("
            SELECT q.*, u.username as creator_username
            FROM olmpiyat_questions q
            LEFT JOIN users u ON q.created_by = u.id
            ORDER BY q.created_at DESC
        ");
        $questions = $stmt->fetchAll();
        
        // Get answers for each question
        foreach ($questions as &$question) {
            $stmt = $pdo->prepare("
                SELECT a.*, u.username
                FROM olmpiyat_answers a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.question_id = ?
                ORDER BY a.created_at ASC
            ");
            $stmt->execute([$question['id']]);
            $question['answers'] = $stmt->fetchAll();
        }
        
        jsonResponse(['questions' => $questions]);
        
    } elseif ($method === 'POST') {
        // Add new question (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        // Handle multipart form data for image upload
        $questionText = $_POST['question_text'] ?? '';
        $imagePath = null;
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../static/olmpiyat_images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($fileExt, $allowedExts)) {
                $fileName = uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = 'olmpiyat_images/' . $fileName;
                }
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO olmpiyat_questions (question_text, image_path, created_by) VALUES (?, ?, ?)");
        $stmt->execute([
            $questionText,
            $imagePath,
            $_SESSION['user_id']
        ]);
        
        jsonResponse(['success' => true, 'id' => $pdo->lastInsertId()]);
        
    } elseif ($method === 'DELETE') {
        // Delete question (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        if (!$id) {
            jsonResponse(['error' => 'ID gerekli'], 400);
        }
        
        // Get image path before deleting
        $stmt = $pdo->prepare("SELECT image_path FROM olmpiyat_questions WHERE id=?");
        $stmt->execute([$id]);
        $question = $stmt->fetch();
        
        // Delete image file if exists
        if ($question && $question['image_path']) {
            $imagePath = __DIR__ . '/../static/' . $question['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM olmpiyat_questions WHERE id=?");
        $stmt->execute([$id]);
        
        jsonResponse(['success' => true]);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
