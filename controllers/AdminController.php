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
        $pageNum = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
        $search = isset($_GET['search']) ? trim((string)$_GET['search']) : (isset($_REQUEST['search']) ? trim((string)$_REQUEST['search']) : '');
        $perPage = 25;

        $result = $this->requestModel->getAllForAdminPaginated($search, $pageNum, $perPage);
        $requests = $result['data'];
        $totalRequests = $result['total'];
        $totalPages = $result['totalPages'];
        $currentPage = $result['currentPage'];
        $ajaxFragment = !empty($_GET['ajax']);

        require '../views/admin/manage-request.php';
    }

    public function updateRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['request_id'];
            $status = $_POST['status'] ?? null;
            $remarks = $_POST['remarks'] ?? null;
            $isVerified = isset($_POST['is_verified']) ? (int)$_POST['is_verified'] : null;

            $ok = true;
            if ($isVerified !== null) {
                $ok = $this->requestModel->updateVerificationStatus($id, $isVerified);
                // Automatically reject request status if ID is rejected
                if ($isVerified === 0) {
                    $this->requestModel->updateStatus($id, 'Rejected', 'Automatically rejected due to ID verification failure.');
                }
            }

            if ($status !== null) {
                $statusOk = $this->requestModel->updateStatus($id, $status, $remarks);
                $ok = $ok && $statusOk;
            }

            if ($ok) {
                $_SESSION['success'] = "Request updated successfully.";
            } else {
                $_SESSION['error'] = "Action not allowed or request not found.";
            }

            $redirectPage = isset($_POST['page_num']) ? max(1, (int)$_POST['page_num']) : 1;
            $redirectUrl = '?page=manage-requests' . ($redirectPage > 1 ? '&page_num=' . $redirectPage : '');
            if (!empty(trim((string)($_POST['search'] ?? '')))) {
                $redirectUrl .= '&search=' . rawurlencode(trim($_POST['search']));
            }
            header("Location: " . $redirectUrl);
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