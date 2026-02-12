<?php

class RequestModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT r.*, u.username as resident_username, c.name as certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Paginated fetch for a specific user ---
    public function getByUserPaginated($userId, $page = 1, $perPage = 5) {
        $offset = ($page - 1) * $perPage;

        // Fetch paginated requests
        $stmt = $this->pdo->prepare("
            SELECT r.*, c.name as certificate_name
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

        // Get total requests count for pagination
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM requests WHERE user_id = ?");
        $countStmt->execute([$userId]);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return [
            'data' => $requests,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function getByUser($userId) {
        // Keep original for compatibility
        $stmt = $this->pdo->prepare("
            SELECT r.*, c.name as certificate_name
            FROM requests r
            JOIN certificates c ON r.certificate_id = c.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($userId, $certificateId, $appointmentDate, $fullName, $civilStatus, $birthday, $address, $contactNumber) {
        $stmt = $this->pdo->prepare("
            INSERT INTO requests 
            (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $certificateId, $appointmentDate, $fullName, $civilStatus, $birthday, $address, $contactNumber]);
    }

    public function updateStatus($id, $status, $remarks) {
        // Enforce allowed status transitions
        $stmt = $this->pdo->prepare("SELECT status FROM requests WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();

        if ($current === false) return false;

        $allowed = [
            'Pending' => ['Approved', 'Rejected', 'Cancelled'],
            'Approved' => ['Completed'],
            'Rejected' => [],
            'Completed' => [],
            'Cancelled' => []
        ];

        // Allow keeping same status (update remarks if needed)
        if ($status === $current) {
            $stmt = $this->pdo->prepare("UPDATE requests SET remarks = ? WHERE id = ?");
            return $stmt->execute([$remarks, $id]);
        }

        if (!isset($allowed[$current]) || !in_array($status, $allowed[$current], true)) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE requests SET status = ?, remarks = ? WHERE id = ?");
        return $stmt->execute([$status, $remarks, $id]);
    }
}