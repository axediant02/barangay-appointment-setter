<?php

class RequestModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /* ============================
       RESIDENT METHODS
    ============================ */

    // Create new request
    public function create($userId, $certificateId, $appointmentDate) {
        $stmt = $this->pdo->prepare("
            INSERT INTO requests (user_id, certificate_id, appointment_date, status, remarks, created_at)
            VALUES (?, ?, ?, 'Pending', '', NOW())
        ");

        return $stmt->execute([$userId, $certificateId, $appointmentDate]);
    }

    // Get requests of a specific resident
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

    // Optional: cancel request if still pending
    public function cancel($requestId, $userId) {
        $stmt = $this->pdo->prepare("
            UPDATE requests
            SET status = 'Cancelled'
            WHERE id = ? AND user_id = ? AND status = 'Pending'
        ");

        return $stmt->execute([$requestId, $userId]);
    }


    /* ============================
       ADMIN METHODS
    ============================ */

    // Get all requests (admin)
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT r.*, 
                   u.name AS resident_name, 
                   c.name AS certificate_name
            FROM requests r
            JOIN users u ON r.user_id = u.id
            JOIN certificates c ON r.certificate_id = c.id
            ORDER BY r.created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin updates status & remarks
    public function updateStatus($id, $status, $remarks) {
        $stmt = $this->pdo->prepare("
            UPDATE requests
            SET status = ?, remarks = ?
            WHERE id = ?
        ");

        return $stmt->execute([$status, $remarks, $id]);
    }
}