<?php
require_once __DIR__ . '/../config/database.php';

class Hotel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllHotels(): array {
        $stmt = $this->db->query("SELECT * FROM hotels ORDER BY star_rating DESC");
        return $stmt->fetchAll();
    }

    public function getHotelById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM hotels WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getRoomTypesByHotel(int $hotelId): array {
        $stmt = $this->db->prepare("SELECT * FROM room_types WHERE hotel_id = ?");
        $stmt->execute([$hotelId]);
        return $stmt->fetchAll();
    }

    /**
     * Search available room types based on destination city, dates, and occupancy
     */
    public function searchAvailableRooms(string $city = '', string $checkIn = '', string $checkOut = '', int $guests = 1): array {
        $sql = "
            SELECT 
                rt.id AS room_type_id,
                rt.type_name,
                rt.base_price_per_night,
                rt.max_occupancy,
                rt.description AS room_description,
                rt.amenities,
                h.id AS hotel_id,
                h.name AS hotel_name,
                h.city,
                h.address,
                h.star_rating,
                h.image_url,
                (
                    SELECT r.id FROM rooms r 
                    WHERE r.room_type_id = rt.id 
                      AND r.status = 'available'
                      AND r.id NOT IN (
                          SELECT b.room_id FROM bookings b 
                          WHERE b.status IN ('confirmed', 'pending')
                            AND (
                              (b.check_in_date < ? AND b.check_out_date > ?)
                            )
                      )
                    LIMIT 1
                ) AS available_room_id
            FROM room_types rt
            JOIN hotels h ON rt.hotel_id = h.id
            WHERE rt.max_occupancy >= ?
        ";

        $params = [$checkOut ?: '2099-12-31', $checkIn ?: '2000-01-01', $guests];

        if (!empty($city)) {
            $sql .= " AND h.city LIKE ?";
            $params[] = "%{$city}%";
        }

        $sql .= " ORDER BY h.star_rating DESC, rt.base_price_per_night ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        // Process amenities (JSON array or comma-separated string)
        foreach ($results as &$row) {
            $row['is_available'] = !empty($row['available_room_id']);
            if (is_string($row['amenities']) && !empty($row['amenities'])) {
                $decoded = json_decode($row['amenities'], true);
                if (is_array($decoded)) {
                    $row['amenities_list'] = $decoded;
                } else {
                    $row['amenities_list'] = array_map('trim', explode(',', $row['amenities']));
                }
            } else {
                $row['amenities_list'] = ['WiFi', 'Smart TV', 'AC'];
            }
        }
        return $results;
    }
}
