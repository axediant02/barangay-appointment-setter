<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

// Controllers used by some actions (eg. cancel-request)
if (file_exists('../controllers/RequestController.php')) {
    require_once '../controllers/RequestController.php';
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? null;
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? null;

try {
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
        
        // Ensure all values are integers (not NULL)
        $stats['total'] = (int) ($stats['total'] ?? 0);
        $stats['pending'] = (int) ($stats['pending'] ?? 0);
        $stats['approved'] = (int) ($stats['approved'] ?? 0);
        $stats['completed'] = (int) ($stats['completed'] ?? 0);
        $stats['rejected'] = (int) ($stats['rejected'] ?? 0);
        
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
            SELECT r.*, u.username as resident_name, c.name as certificate_name
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
            SELECT r.*, u.username as resident_name, c.name as certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
        ");
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($requests);
        break;

    // Alias used by admin live table polling in manage-request.php
    case 'admin-requests':
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        // Respect the same pagination as manage-requests (10 per page)
        $pageNum = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
        $perPage = 10;
        $offset = ($pageNum - 1) * $perPage;

        $stmt = $pdo->prepare("
            SELECT r.*, u.username as resident_name, c.name as certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize shape a bit for the frontend (format appointment_date)
        $normalized = [];
        foreach ($rows as $row) {
            if (!empty($row['appointment_date'])) {
                $row['appointment_date'] = date('M d, Y', strtotime($row['appointment_date']));
            }
            $normalized[] = $row;
        }

        echo json_encode($normalized);
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
} catch (Throwable $e) {
    // Ensure we always return JSON, even on unexpected errors
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?>
