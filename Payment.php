<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function processPayment(int $bookingId, string $method, float $amount): array|bool {
        try {
            $this->db->beginTransaction();

            $txRef = 'PAY-' . strtoupper(substr($method, 0, 4)) . '-' . rand(10000, 99999);

            // Insert payment log
            $stmt = $this->db->prepare("
                INSERT INTO payments (booking_id, payment_method, transaction_reference, amount_paid, payment_status)
                VALUES (?, ?, ?, ?, 'success')
            ");
            $stmt->execute([$bookingId, $method, $txRef, $amount]);
            $paymentId = (int)$this->db->lastInsertId();

            // Update booking status to confirmed
            $updateStmt = $this->db->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
            $updateStmt->execute([$bookingId]);

            $this->db->commit();

            return [
                'payment_id' => $paymentId,
                'transaction_ref' => $txRef,
                'method' => $method,
                'amount' => $amount,
                'status' => 'success'
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function getPaymentByBookingId(int $bookingId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE booking_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$bookingId]);
        return $stmt->fetch() ?: null;
    }
}
