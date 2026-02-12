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

    // ----------------------------
    // Show request creation form
    // ----------------------------
    public function createForm() {

        $certificates = $this->certificateModel->getAll();

        $stmt = $this->pdo->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ---------------------------------
        // Get certificates requested today
        // ---------------------------------
        $stmt = $this->pdo->prepare("
            SELECT certificate_id 
            FROM requests 
            WHERE user_id = ? 
            AND DATE(created_at) = CURDATE()
            AND status != 'Cancelled'
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $userRequestsToday = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // ---------------------------------
        // Get cancellation counts per certificate
        // ---------------------------------
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

    // ----------------------------
    // Store new request
    // ----------------------------
    public function store() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $certificateId   = $_POST['certificate_id'] ?? null;
            $appointmentDate = $_POST['appointment_date'] ?? null;
            $fullName        = $_POST['full_name'] ?? null;
            $civilStatus     = $_POST['civil_status'] ?? null;
            $birthday        = $_POST['birthday'] ?? null;
            $address         = $_POST['address'] ?? null;
            $contactNumber   = $_POST['contact_number'] ?? null;

            if (!$certificateId || !$appointmentDate || !$fullName || !$address || !$contactNumber) {
                $_SESSION['error'] = "All required fields must be filled.";
                header("Location: ?page=create-request");
                exit;
            }

            // ---------------------------------
            // Check permanent ban (3 cancellations)
            // ---------------------------------
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
                $_SESSION['error'] = "You cannot request this certificate because you reached the maximum of 3 cancellations.";
                header("Location: ?page=create-request");
                exit;
            }

            // ---------------------------------
            // Daily active request check
            // ---------------------------------
            if (!$this->requestModel->canCreateRequest($_SESSION['user_id'], $certificateId)) {
                $_SESSION['error'] = "You already have an active request for this certificate today.";
                header("Location: ?page=create-request");
                exit;
            }

            // Create the request
            $this->requestModel->create(
                $_SESSION['user_id'],
                $certificateId,
                $appointmentDate,
                $fullName,
                $civilStatus,
                $birthday,
                $address,
                $contactNumber
            );

            $_SESSION['success'] = "Request submitted successfully.";
            header("Location: ?page=my-requests");
            exit;
        }
    }

    // ----------------------------
    // Cancel request (only if Pending)
    // ----------------------------
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
                $_SESSION['error'] = "Request cannot be cancelled. It may already be processed.";
                header("Location: ?page=my-requests");
                exit;
            }

            $certificateId = $req['certificate_id'];

            if (!$this->requestModel->canCancelRequest($_SESSION['user_id'], $certificateId)) {
                $_SESSION['error'] = "You have reached the maximum of 3 cancellations for this certificate.";
                header("Location: ?page=my-requests");
                exit;
            }

            $stmt = $this->pdo->prepare("
                UPDATE requests
                SET status = 'Cancelled'
                WHERE id = ? AND user_id = ? AND status = 'Pending'
            ");
            $stmt->execute([$requestId, $_SESSION['user_id']]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Request cancelled successfully.";
            } else {
                $_SESSION['error'] = "Request could not be cancelled. It may already be processed.";
            }

            header("Location: ?page=my-requests");
            exit;
        }
    }

    // ----------------------------
    // Resident: My Requests
    // ----------------------------
    public function myRequests() {

        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $perPage = 5;

        $paginated = $this->requestModel->getByUserPaginated($_SESSION['user_id'], $page, $perPage);

        $requests    = $paginated['data'] ?? [];
        $totalPages  = $paginated['totalPages'] ?? 1;
        $currentPage = $paginated['currentPage'] ?? 1;

        // ---------------------------------
        // Get cancellation counts per certificate
        // ---------------------------------
        $stmt = $this->pdo->prepare("
            SELECT certificate_id, COUNT(*) as total
            FROM requests
            WHERE user_id = ?
            AND status = 'Cancelled'
            GROUP BY certificate_id
        ");
        $stmt->execute([$_SESSION['user_id']]);
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

    // ----------------------------
    // Admin: All Requests
    // ----------------------------
    public function allRequests() {

        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $perPage = 10;

        $paginated = $this->requestModel->getAllPaginated($page, $perPage);

        $requests    = $paginated['data'] ?? [];
        $totalPages  = $paginated['totalPages'] ?? 1;
        $currentPage = $paginated['currentPage'] ?? 1;

        require '../views/admin/all-requests.php';
    }
}