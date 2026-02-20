<?php

class Certificate {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM certificates ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO certificates (name, description, created_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$name, $description]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM certificates WHERE id = ?");
        return $stmt->execute([$id]);
    }
}