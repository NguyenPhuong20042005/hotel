<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function register(string $fullName, string $email, string $password, string $phone = '', string $city = 'Ha Noi'): bool|array {
        // Check duplicate email
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO users (full_name, email, password_hash, phone, city, role) 
            VALUES (?, ?, ?, ?, ?, 'customer')
        ");
        $stmt->execute([$fullName, $email, $passwordHash, $phone, $city]);

        $userId = $this->db->lastInsertId();
        return $this->findById((int)$userId);
    }

    public function login(string $email, string $password): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
        return null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, full_name, email, phone, city, country, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function updateProfile(int $id, string $fullName, string $phone, string $city): bool {
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ?, city = ? WHERE id = ?");
        return $stmt->execute([$fullName, $phone, $city, $id]);
    }
}
