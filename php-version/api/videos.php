<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../functions.php';

header('Content-Type: application/json');

// Prevent browser caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

$method = $_SERVER['REQUEST_METHOD'];

// Parse ID from URL (e.g., /api/videos/123 or /api/videos.php?id=123)
$requestUri = $_SERVER['REQUEST_URI'];
$id = null;
if (preg_match('/\/api\/videos\.php\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
} elseif (preg_match('/\/api\/videos\/(\d+)/', $requestUri, $matches)) {
    $id = $matches[1];
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
}

try {
    $pdo = getDB();
    
    if ($method === 'GET') {
        // Get all videos
        $stmt = $pdo->query("SELECT * FROM videos ORDER BY order_index ASC, created_at DESC");
        $videos = $stmt->fetchAll();
        jsonResponse(['videos' => $videos]);
        
    } elseif ($method === 'POST') {
        // Add new video (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO videos (title, youtube_url, description, order_index) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['youtube_url'],
            $data['description'] ?? '',
            $data['order_index'] ?? 0
        ]);
        
        jsonResponse(['success' => true, 'id' => $pdo->lastInsertId()]);
        
    } elseif ($method === 'PUT') {
        // Update video (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        if (!$id) {
            jsonResponse(['error' => 'ID gerekli'], 400);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("UPDATE videos SET title=?, youtube_url=?, description=?, order_index=? WHERE id=?");
        $stmt->execute([
            $data['title'],
            $data['youtube_url'],
            $data['description'] ?? '',
            $data['order_index'] ?? 0,
            $id
        ]);
        
        jsonResponse(['success' => true]);
        
    } elseif ($method === 'DELETE') {
        // Delete video (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        if (!$id) {
            jsonResponse(['error' => 'ID gerekli'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id=?");
        $stmt->execute([$id]);
        
        jsonResponse(['success' => true]);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
