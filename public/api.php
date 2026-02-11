<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? null;
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? null;

switch ($action) {
    case 'resident-stats':
        if ($role !== 'resident') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(status = 'Pending') as pending,
                SUM(status = 'Approved') as approved,
                SUM(status = 'Completed') as completed,
                SUM(status = 'Rejected') as rejected
            FROM requests
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($stats);
        break;

    case 'my-requests':
        if ($role !== 'resident') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $stmt = $pdo->prepare("
            SELECT r.*, c.name AS certificate_name
            FROM requests r
            JOIN certificates c ON r.certificate_id = c.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$userId]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($requests);
        break;

    case 'admin-stats':
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'resident'");
        $totalResidents = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(status = 'Pending') as pending,
                SUM(status = 'Approved') as approved,
                SUM(status = 'Completed') as completed,
                SUM(status = 'Rejected') as rejected
            FROM requests
        ");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['residents'] = $totalResidents;
        echo json_encode($stats);
        break;

    case 'admin-recent-requests':
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $stmt = $pdo->query("
            SELECT r.*, u.name as resident_name, c.name as certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
            LIMIT 10
        ");
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($requests);
        break;

    case 'manage-requests':
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $stmt = $pdo->query("
            SELECT r.*, u.name as resident_name, c.name as certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
        ");
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($requests);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}
?>
