<?php

class RequestModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT r.*, u.username AS resident_username, c.name AS certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserPaginated($userId, $page = 1, $perPage = 5) {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare("
            SELECT r.*, c.name AS certificate_name
            FROM requests r
            JOIN certificates c ON r.certificate_id = c.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM requests WHERE user_id = ?");
        $countStmt->execute([$userId]);
        $total = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        return [
            'data' => $requests,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function getAllPaginated($page = 1, $perPage = 25) {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare("
            SELECT r.*, u.username AS resident_username, c.name AS certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->pdo->query("SELECT COUNT(*) AS total FROM requests");
        $total = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        return [
            'data' => $requests,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, c.name AS certificate_name
            FROM requests r
            JOIN certificates c ON r.certificate_id = c.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array|null Request row with certificate_name for the given user */
    public function findByIdAndUser($id, $userId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, c.name AS certificate_name
            FROM requests r
            JOIN certificates c ON r.certificate_id = c.id
            WHERE r.id = ? AND r.user_id = ?
        ");
        $stmt->execute([$id, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateResidentRequest($id, $userId, $fullName, $civilStatus, $birthday, $address, $contactNumber, $appointmentDate) {
        $stmt = $this->pdo->prepare("
            UPDATE requests
            SET full_name = ?, civil_status = ?, birthday = ?, address = ?, contact_number = ?, appointment_date = ?
            WHERE id = ? AND user_id = ? AND status = 'Pending'
        ");
        return $stmt->execute([$fullName, $civilStatus, $birthday ?: null, $address, $contactNumber, $appointmentDate, $id, $userId]);
    }

    public function create($userId, $certificateId, $appointmentDate, $fullName, $civilStatus, $birthday, $address, $contactNumber) {
        $stmt = $this->pdo->prepare("
            INSERT INTO requests 
            (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $certificateId, $appointmentDate, $fullName, $civilStatus, $birthday, $address, $contactNumber]);
    }

    public function updateStatus($id, $status, $remarks = null) {
        $stmt = $this->pdo->prepare("SELECT status, certificate_id, user_id FROM requests WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return false;

        $current = $row['status'];

        $allowed = [
            'Pending'   => ['Approved', 'Rejected', 'Cancelled'],
            'Approved'  => ['Completed'],
            'Rejected'  => [],
            'Completed' => [],
            'Cancelled' => []
        ];

        if ($status === $current) {
            $stmt = $this->pdo->prepare("UPDATE requests SET remarks = ? WHERE id = ?");
            return $stmt->execute([$remarks, $id]);
        }

        if (!isset($allowed[$current]) || !in_array($status, $allowed[$current], true)) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE requests SET status = ?, remarks = ? WHERE id = ?");
        return $stmt->execute([$status, $remarks, $id]);
        return $stmt->execute([$status, $remarks, $id]);
    }

    public function countUserActiveRequestsForCertificateToday($userId, $certificateId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM requests 
            WHERE user_id = ? 
              AND certificate_id = ? 
              AND status != 'Cancelled'
              AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$userId, $certificateId]);
        return (int)$stmt->fetchColumn();
    }

    public function countUserCancellationsForCertificate($userId, $certificateId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM requests 
            WHERE user_id = ? 
              AND certificate_id = ? 
              AND status = 'Cancelled'
        ");
        $stmt->execute([$userId, $certificateId]);
        return (int)$stmt->fetchColumn();
    }

    public function canCancelRequest($userId, $certificateId) {
        return $this->countUserCancellationsForCertificate($userId, $certificateId) < 3;
    }

    public function isTemporarilyBanned($userId, $certificateId) {
        return $this->countUserCancellationsForCertificate($userId, $certificateId) >= 3;
    }


    public function canCreateRequest($userId, $certificateId) {
        if ($this->isTemporarilyBanned($userId, $certificateId)) {
            return false;
        }

        if ($this->countUserActiveRequestsForCertificateToday($userId, $certificateId) > 0) {
            return false;
        }

        return true;
    }
}