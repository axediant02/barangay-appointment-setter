<?php

require_once '../models/Certificate.php';
require_once '../models/Request.php';

class RequestController {

    private $certificateModel;
    private $requestModel;

    public function __construct($pdo) {
        $this->certificateModel = new Certificate($pdo);
        $this->requestModel = new RequestModel($pdo);
    }

    public function createForm() {
        $certificates = $this->certificateModel->getAll();
        require '../views/resident/create-request.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $certificateId = $_POST['certificate_id'] ?? null;
            $appointmentDate = $_POST['appointment_date'] ?? null;

            if (!$certificateId || !$appointmentDate) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: ?page=create-request");
                exit;
            }

            $this->requestModel->create(
                $_SESSION['user_id'],
                $certificateId,
                $appointmentDate
            );

            $_SESSION['success'] = "Request submitted successfully.";
            header("Location: ?page=my-requests");
            exit;
        }
    }

    public function myRequests() {
        $requests = $this->requestModel->getByUser($_SESSION['user_id']);
        require '../views/resident/my-request.php';
    }
}