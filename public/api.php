<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

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
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
            FROM requests
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get latest update timestamp for change detection
        // Handle case where updated_at column might not exist
        try {
            $stmt = $pdo->prepare("
                SELECT COALESCE(MAX(updated_at), MAX(created_at), NOW()) as last_update
                FROM requests
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $lastUpdate = $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Fallback to created_at if updated_at doesn't exist
            $stmt = $pdo->prepare("
                SELECT COALESCE(MAX(created_at), NOW()) as last_update
                FROM requests
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $lastUpdate = $stmt->fetchColumn();
        }

        echo json_encode([
            'total' => (int)($stats['total'] ?? 0),
            'pending' => (int)($stats['pending'] ?? 0),
            'approved' => (int)($stats['approved'] ?? 0),
            'completed' => (int)($stats['completed'] ?? 0),
            'rejected' => (int)($stats['rejected'] ?? 0),
            'last_update' => $lastUpdate
        ]);
        break;

    case 'resident-check-updates':
        if ($role !== 'resident') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $since = $_GET['since'] ?? null;
        if (!$since) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing since parameter']);
            exit;
        }
        
        // Check if any requests were updated since the given timestamp
        // Handle case where updated_at column might not exist
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as has_updates,
                       COALESCE(MAX(updated_at), MAX(created_at), NOW()) as last_update
                FROM requests
                WHERE user_id = ?
                  AND (COALESCE(updated_at, created_at) > ? OR created_at > ?)
            ");
            $stmt->execute([$userId, $since, $since]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback to created_at if updated_at doesn't exist
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as has_updates,
                       COALESCE(MAX(created_at), NOW()) as last_update
                FROM requests
                WHERE user_id = ?
                  AND created_at > ?
            ");
            $stmt->execute([$userId, $since]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        echo json_encode([
            'has_updates' => (int)($result['has_updates'] ?? 0) > 0,
            'last_update' => $result['last_update'] ?? null
        ]);
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

    case 'cancel-request':
        $controller = new RequestController($pdo);
        $controller->cancel();
        break;

    case 'admin-stats':
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'resident'");
        $totalResidents = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

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

        echo json_encode([
            'total' => (int)($stats['total'] ?? 0),
            'pending' => (int)($stats['pending'] ?? 0),
            'approved' => (int)($stats['approved'] ?? 0),
            'completed' => (int)($stats['completed'] ?? 0),
            'rejected' => (int)($stats['rejected'] ?? 0),
            'residents' => $totalResidents
        ]);
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
        $totalResidents = (int)($stmt->fetchColumn() ?: 0);

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
        
        echo json_encode([
            'stats' => [
                'total' => (int)($stats['total'] ?? 0),
                'pending' => (int)($stats['pending'] ?? 0),
                'approved' => (int)($stats['approved'] ?? 0),
                'completed' => (int)($stats['completed'] ?? 0),
                'rejected' => (int)($stats['rejected'] ?? 0),
                'residents' => $totalResidents
            ]
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}
?>