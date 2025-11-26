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

try {
    $pdo = getDB();
    
    if ($method === 'GET') {
        // Get site info
        $stmt = $pdo->query("SELECT `key`, value FROM site_info");
        $rows = $stmt->fetchAll();
        
        $siteInfo = [];
        foreach ($rows as $row) {
            $siteInfo[$row['key']] = $row['value'];
        }
        
        jsonResponse(['site_info' => $siteInfo]);
        
    } elseif ($method === 'PUT') {
        // Update site info (admin only)
        if (!isAdmin()) {
            jsonResponse(['error' => 'Yetkiniz yok'], 403);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("INSERT INTO site_info (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value=?");
        
        foreach ($data as $key => $value) {
            $stmt->execute([$key, $value, $value]);
        }
        
        jsonResponse(['success' => true]);
    }
    
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
