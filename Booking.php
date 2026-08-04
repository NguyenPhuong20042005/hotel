<?php
require_once __DIR__ . '/../config/database.php';

class Booking {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createBooking(int $userId, int $roomId, string $checkIn, string $checkOut, int $guests, float $totalAmount): array|bool {
        try {
            $this->db->beginTransaction();

            // 1. Double check room availability under transaction
            $checkStmt = $this->db->prepare("
                SELECT id FROM bookings 
                WHERE room_id = ? 
                  AND status IN ('confirmed', 'pending')
                  AND (check_in_date < ? AND check_out_date > ?)
            ");
            $checkStmt->execute([$roomId, $checkOut, $checkIn]);
            if ($checkStmt->fetch()) {
                $this->db->rollBack();
                return false; // Room is already reserved for these dates
            }

            // 2. Generate unique booking code
            $bookingCode = 'GVB-' . date('Y') . '-' . rand(1000, 9999);

            // 3. Insert Booking
            $stmt = $this->db->prepare("
                INSERT INTO bookings (booking_code, user_id, room_id, check_in_date, check_out_date, total_guests, total_amount, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$bookingCode, $userId, $roomId, $checkIn, $checkOut, $guests, $totalAmount]);
            $bookingId = (int)$this->db->lastInsertId();

            $this->db->commit();
            return $this->getBookingById($bookingId);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function getBookingById(int $bookingId): ?array {
        $stmt = $this->db->prepare("
            SELECT b.*, u.full_name AS customer_name, u.email AS customer_email, u.phone AS customer_phone,
                   r.room_number, rt.type_name, h.name AS hotel_name, h.address AS hotel_address, h.city AS hotel_city
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            JOIN room_types rt ON r.room_type_id = rt.id
            JOIN hotels h ON rt.hotel_id = h.id
            WHERE b.id = ?
        ");
        $stmt->execute([$bookingId]);
        return $stmt->fetch() ?: null;
    }

    public function getBookingsByUserId(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT b.*, r.room_number, rt.type_name, h.name AS hotel_name, p.payment_status, p.payment_method
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            JOIN room_types rt ON r.room_type_id = rt.id
            JOIN hotels h ON rt.hotel_id = h.id
            LEFT JOIN payments p ON p.booking_id = b.id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function cancelBooking(int $bookingId, int $userId): bool {
        $stmt = $this->db->prepare("
            UPDATE bookings SET status = 'cancelled' 
            WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed')
        ");
        return $stmt->execute([$bookingId, $userId]);
    }
}
