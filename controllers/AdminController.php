<?php

require_once '../models/Request.php';
require_once '../models/Certificate.php';

class AdminController {

    private $requestModel;
    private $certificateModel;

    public function __construct($pdo) {
        $this->requestModel = new RequestModel($pdo);
        $this->certificateModel = new Certificate($pdo);
    }

    public function manageRequests() {
        $pageNum = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $perPage = 10;
        
        $requests = $this->requestModel->getAll();
        $totalRequests = count($requests);
        $totalPages = ceil($totalRequests / $perPage);
        $offset = ($pageNum - 1) * $perPage;
        
        $requests = array_slice($requests, $offset, $perPage);
        $currentPage = $pageNum;
        
        require '../views/admin/manage-request.php';
    }

    public function updateRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'];

            $ok = $this->requestModel->updateStatus($id, $status, $remarks);

            if ($ok) {
                $_SESSION['success'] = "Request updated successfully.";
            } else {
                $_SESSION['error'] = "Invalid status transition or request not found. Action not allowed.";
            }

            header("Location: ?page=manage-requests");
            exit;
        }
    }

    public function manageCertificates() {
        $certificates = $this->certificateModel->getAll();
        require '../views/admin/manage-certificates.php';
    }

    public function createCertificate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = $_POST['name'];
            $description = $_POST['description'];

            $this->certificateModel->create($name, $description);

            header("Location: ?page=manage-certificates");
            exit;
        }
    }

    public function deleteCertificate() {
        $id = $_GET['id'];
        $this->certificateModel->delete($id);

        header("Location: ?page=manage-certificates");
        exit;
    }
}