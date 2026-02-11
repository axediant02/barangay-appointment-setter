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

    // Show request form
    public function createForm() {
        $certificates = $this->certificateModel->getAll();

        // Optional: pre-fill username and email
        $stmt = $this->pdo->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        require '../views/resident/create-request.php';
    }

    // Store request with resident info snapshot
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

    // Show resident's requests
    public function myRequests() {
        $requests = $this->requestModel->getByUser($_SESSION['user_id']);
        require '../views/resident/my-request.php';
    }
}