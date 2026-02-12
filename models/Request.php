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

    public function getByUser($userId) {
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
        $stmt = $this->pdo->prepare("
            UPDATE requests
            SET status = ?, remarks = ?
            WHERE id = ?
        ");
            // Enforce allowed status transitions
            $stmt = $this->pdo->prepare("SELECT status FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetchColumn();

            if ($current === false) {
                return false;
            }

            $allowed = [
                'Pending' => ['Approved', 'Rejected'],
                'Approved' => ['Completed'],
                'Rejected' => [],
                'Completed' => [],
                'Cancelled' => []
            ];

            // If current status not recognized, disallow change
            if (!isset($allowed[$current])) {
                return false;
            }

            // Allow keeping same status
            if ($status === $current) {
                // Still update remarks if provided
                $stmt = $this->pdo->prepare("UPDATE requests SET remarks = ? WHERE id = ?");
                return $stmt->execute([$remarks, $id]);
            }

            if (!in_array($status, $allowed[$current], true)) {
                return false;
            }

            $stmt = $this->pdo->prepare("UPDATE requests SET status = ?, remarks = ? WHERE id = ?");
            return $stmt->execute([$status, $remarks, $id]);
    }
}