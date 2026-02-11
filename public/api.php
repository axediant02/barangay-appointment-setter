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
            LIMIT 8
        ");
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the response for frontend
        $formattedRequests = [];
        foreach ($requests as $req) {
            $formattedRequests[] = [
                'id' => $req['id'],
                'resident_name' => $req['resident_name'],
                'certificate_name' => $req['certificate_name'],
                'status' => $req['status'],
                'created_at' => $req['created_at'],
                'created_at_formatted' => date('M d, Y', strtotime($req['created_at']))
            ];
        }
        
        echo json_encode($formattedRequests);
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

    case 'admin-dashboard-sync':
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'resident'");
        $totalResidents = $stmt->fetchColumn() ?: 0;

        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
            FROM requests
        ");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Ensure all values are integers, not NULL
        $stats['residents'] = (int) $totalResidents;
        $stats['total'] = (int) ($stats['total'] ?? 0);
        $stats['pending'] = (int) ($stats['pending'] ?? 0);
        $stats['approved'] = (int) ($stats['approved'] ?? 0);
        $stats['completed'] = (int) ($stats['completed'] ?? 0);
        $stats['rejected'] = (int) ($stats['rejected'] ?? 0);
        
        echo json_encode(['stats' => $stats]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}
?>
