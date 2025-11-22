<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDB();
    
    if ($method === 'GET') {
        // Get all documents
        $stmt = $pdo->query("SELECT * FROM documents ORDER BY order_index ASC, created_at DESC");
        $documents = $stmt->fetchAll();
        jsonResponse(['documents' => $documents]);
        
    } elseif ($method === 'POST') {
        // Add new document (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $orderIndex = $_POST['order_index'] ?? 0;
        
        if (isset($_FILES['file'])) {
            $result = uploadFile($_FILES['file'], DOCUMENTS_FOLDER);
            if (!$result['success']) {
                jsonResponse(['error' => $result['error']], 400);
            }
            
            $stmt = $pdo->prepare("INSERT INTO documents (title, description, file_path, order_index) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, 'documents/' . $result['filename'], $orderIndex]);
            
            jsonResponse(['success' => true, 'id' => $pdo->lastInsertId()]);
        } else {
            jsonResponse(['error' => 'Dosya gerekli'], 400);
        }
        
    } elseif ($method === 'PUT') {
        // Update document (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            jsonResponse(['error' => 'ID gerekli'], 400);
        }
        
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $orderIndex = $_POST['order_index'] ?? 0;
        
        if (isset($_FILES['file'])) {
            // Delete old file
            $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id=?");
            $stmt->execute([$id]);
            $oldDoc = $stmt->fetch();
            if ($oldDoc) {
                deleteFile(__DIR__ . '/../static/' . $oldDoc['file_path']);
            }
            
            $result = uploadFile($_FILES['file'], DOCUMENTS_FOLDER);
            if (!$result['success']) {
                jsonResponse(['error' => $result['error']], 400);
            }
            
            $stmt = $pdo->prepare("UPDATE documents SET title=?, description=?, file_path=?, order_index=? WHERE id=?");
            $stmt->execute([$title, $description, 'documents/' . $result['filename'], $orderIndex, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE documents SET title=?, description=?, order_index=? WHERE id=?");
            $stmt->execute([$title, $description, $orderIndex, $id]);
        }
        
        jsonResponse(['success' => true]);
        
    } elseif ($method === 'DELETE') {
        // Delete document (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            jsonResponse(['error' => 'ID gerekli'], 400);
        }
        
        // Delete file
        $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id=?");
        $stmt->execute([$id]);
        $doc = $stmt->fetch();
        if ($doc) {
            deleteFile(__DIR__ . '/../static/' . $doc['file_path']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id=?");
        $stmt->execute([$id]);
        
        jsonResponse(['success' => true]);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
