<?php

require_once '../models/Certificate.php';
require_once '../models/Request.php';

class RequestController {

    private $certificateModel;
    private $requestModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->certificateModel = new Certificate($pdo);
        $this->requestModel = new RequestModel($pdo);
    }

    public function createForm() {

        $certificates = $this->certificateModel->getAll();

        $stmt = $this->pdo->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("
            SELECT certificate_id 
            FROM requests 
            WHERE user_id = ? 
            AND DATE(created_at) = CURDATE()
            AND status != 'Cancelled'
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $userRequestsToday = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $this->pdo->prepare("
            SELECT certificate_id, COUNT(*) as total
            FROM requests
            WHERE user_id = ?
            AND status = 'Cancelled'
            GROUP BY certificate_id
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cancellations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userBanStatus = [];
        foreach ($cancellations as $row) {
            if ($row['total'] >= 3) {
                $userBanStatus[$row['certificate_id']] = true;
            }
        }

        require '../views/resident/create-request.php';
    }

    public function store() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $certificateId   = $_POST['certificate_id'] ?? null;
            $appointmentDate = $_POST['appointment_date'] ?? null;
            $fullName        = $_POST['full_name'] ?? null;
            $civilStatus     = $_POST['civil_status'] ?? null;
            $birthday        = $_POST['birthday'] ?? null;
            $address         = $_POST['address'] ?? null;
            $contactNumber   = $_POST['contact_number'] ?? null;
            
            // File Upload Handling
            $file = $_FILES['id_image'] ?? null;
            $idImagePath = null;

            if (!$certificateId || !$appointmentDate || !$fullName || !$address || !$contactNumber) {
                $_SESSION['error'] = "All required fields must be filled.";
                header("Location: ?page=create-request");
                exit;
            }

            // Validate File
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                if (!in_array($file['type'], $allowedTypes)) {
                    $_SESSION['error'] = "Only JPG, PNG, and WebP images are allowed for ID.";
                    header("Location: ?page=create-request");
                    exit;
                }
                
                if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
                    $_SESSION['error'] = "ID image must be less than 5MB.";
                    header("Location: ?page=create-request");
                    exit;
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'req_' . time() . '_' . uniqid() . '.' . $ext;
                $targetDir = '../public/uploads/request_ids/';
                
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
                    $idImagePath = 'public/uploads/request_ids/' . $filename;
                } else {
                    $_SESSION['error'] = "Failed to upload ID image. Please try again.";
                    header("Location: ?page=create-request");
                    exit;
                }
            } else {
                $_SESSION['error'] = "A valid ID photo is required.";
                header("Location: ?page=create-request");
                exit;
            }

            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM requests 
                WHERE user_id = ? 
                AND certificate_id = ? 
                AND status = 'Cancelled'
            ");
            $stmt->execute([$_SESSION['user_id'], $certificateId]);
            $cancelCount = $stmt->fetchColumn();

            if ($cancelCount >= 3) {
                $_SESSION['error'] = "You reached the maximum of 3 cancellations for this certificate.";
                header("Location: ?page=create-request");
                exit;
            }

            if (!$this->requestModel->canCreateRequest($_SESSION['user_id'], $certificateId)) {
                $_SESSION['error'] = "You already have an active request for this certificate today.";
                header("Location: ?page=create-request");
                exit;
            }

            $this->requestModel->create(
                $_SESSION['user_id'],
                $certificateId,
                $appointmentDate,
                $fullName,
                $civilStatus,
                $birthday,
                $address,
                $contactNumber,
                $idImagePath // Pass image path
            );

            $_SESSION['success'] = "Request submitted successfully.";
            header("Location: ?page=my-requests");
            exit;
        }
    }

    public function cancel() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $requestId = $_POST['request_id'] ?? null;

            if (!$requestId) {
                $_SESSION['error'] = "Invalid request.";
                header("Location: ?page=my-requests");
                exit;
            }

            $stmt = $this->pdo->prepare("
                SELECT certificate_id 
                FROM requests 
                WHERE id = ? 
                AND user_id = ? 
                AND status = 'Pending'
            ");
            $stmt->execute([$requestId, $_SESSION['user_id']]);
            $req = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$req) {
                $_SESSION['error'] = "Request cannot be cancelled.";
                header("Location: ?page=my-requests");
                exit;
            }

            $certificateId = $req['certificate_id'];

            if (!$this->requestModel->canCancelRequest($_SESSION['user_id'], $certificateId)) {
                $_SESSION['error'] = "You reached the maximum of 3 cancellations for this certificate.";
                header("Location: ?page=my-requests");
                exit;
            }

            $stmt = $this->pdo->prepare("
                UPDATE requests
                SET status = 'Cancelled'
                WHERE id = ? AND user_id = ? AND status = 'Pending'
            ");
            $stmt->execute([$requestId, $_SESSION['user_id']]);

            $_SESSION['success'] = "Request cancelled successfully.";
            header("Location: ?page=my-requests");
            exit;
        }
    }

    public function myRequests() {

        $userId = $_SESSION['user_id'];

        $limit = 5;
        $currentPage = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
        $offset = ($currentPage - 1) * $limit;

        $countStmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM requests 
            WHERE user_id = ?
        ");
        $countStmt->execute([$userId]);
        $totalRecords = (int) $countStmt->fetchColumn();

        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

        $stmt = $this->pdo->prepare("
            SELECT r.*, c.name as certificate_name
            FROM requests r
            JOIN certificates c ON r.certificate_id = c.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();

        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("
            SELECT certificate_id, COUNT(*) as total
            FROM requests
            WHERE user_id = ?
            AND status = 'Cancelled'
            GROUP BY certificate_id
        ");
        $stmt->execute([$userId]);
        $cancellations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userCancellationCounts = [];
        $userBanStatus = [];

        foreach ($cancellations as $row) {
            $userCancellationCounts[$row['certificate_id']] = $row['total'];
            if ($row['total'] >= 3) {
                $userBanStatus[$row['certificate_id']] = true;
            }
        }

        require '../views/resident/my-request.php';
    }

    public function viewRequest() {
        $id = (int) ($_GET['id'] ?? 0);

        $request = $this->requestModel->findByIdAndUser($id, $_SESSION['user_id']);

        if (!$request) {
            $_SESSION['error'] = 'Request not found.';
            header('Location: ?page=my-requests');
            exit;
        }

        require '../views/resident/view-request.php';
    }

    public function editRequest() {

        $id = (int) ($_GET['id'] ?? 0);
        $request = $this->requestModel->findByIdAndUser($id, $_SESSION['user_id']);

        if (!$request) {
            $_SESSION['error'] = 'Request not found.';
            header('Location: ?page=my-requests');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $fullName        = trim($_POST['full_name'] ?? '');
            $civilStatus     = trim($_POST['civil_status'] ?? '');
            $birthday        = trim($_POST['birthday'] ?? '') ?: null;
            $address         = trim($_POST['address'] ?? '');
            $contactNumber   = trim($_POST['contact_number'] ?? '');
            $appointmentDate = trim($_POST['appointment_date'] ?? '');

            if (!$fullName || !$address || !$contactNumber || !$appointmentDate) {
                $_SESSION['error'] = 'All required fields must be filled.';
                header("Location: ?page=edit-request&id=$id");
                exit;
            }

            $ok = $this->requestModel->updateResidentRequest(
                $id,
                $_SESSION['user_id'],
                $fullName,
                $civilStatus,
                $birthday,
                $address,
                $contactNumber,
                $appointmentDate
            );

            if ($ok) {
                $_SESSION['success'] = 'Request updated successfully.';
                header("Location: ?page=view-request&id=$id");
                exit;
            }

            $_SESSION['error'] = 'Request could not be updated.';
            header("Location: ?page=edit-request&id=$id");
            exit;
        }

        require '../views/resident/edit-request.php';
    }

    // public function allRequests() {

    //     $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
    //     $perPage = 10;

    //     $paginated = $this->requestModel->getAllPaginated($page, $perPage);

    //     $requests    = $paginated['data'] ?? [];
    //     $totalPages  = $paginated['totalPages'] ?? 1;
    //     $currentPage = $paginated['currentPage'] ?? 1;

    //     require '../views/admin/all-requests.php';
    // }
}