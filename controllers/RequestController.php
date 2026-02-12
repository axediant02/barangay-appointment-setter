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

        require '../views/resident/create-request.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $certificateId = $_POST['certificate_id'] ?? null;
            $appointmentDate = $_POST['appointment_date'] ?? null;
            $fullName = $_POST['full_name'] ?? null;
            $civilStatus = $_POST['civil_status'] ?? null;
            $birthday = $_POST['birthday'] ?? null;
            $address = $_POST['address'] ?? null;
            $contactNumber = $_POST['contact_number'] ?? null;

            if (!$certificateId || !$appointmentDate || !$fullName || !$address || !$contactNumber) {
                $_SESSION['error'] = "All required fields must be filled.";
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
                $contactNumber
            );

            $_SESSION['success'] = "Request submitted successfully.";
            header("Location: ?page=my-requests");
            exit;
        }
    }

    // ✅ CANCEL FEATURE (Only if Pending)
    public function cancel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $requestId = $_POST['request_id'] ?? null;

            if (!$requestId) {
                $_SESSION['error'] = "Invalid request.";
                header("Location: ?page=my-requests");
                exit;
            }

            $stmt = $this->pdo->prepare("
                UPDATE requests
                SET status = 'Cancelled'
                WHERE id = ?
                AND user_id = ?
                AND status = 'Pending'
            ");

            $stmt->execute([
                $requestId,
                $_SESSION['user_id']
            ]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Request cancelled successfully.";
            } else {
                $_SESSION['error'] = "Request cannot be cancelled. It may already be processed.";
            }

            header("Location: ?page=my-requests");
            exit;
        }
    }

    public function myRequests() {
        $requests = $this->requestModel->getByUser($_SESSION['user_id']);
        require '../views/resident/my-request.php';
    }
}