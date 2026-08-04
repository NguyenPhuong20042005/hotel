<?php
/**
 * Centralized Database Connection Manager
 * Supports PDO MySQL and auto-fallback to SQLite for standalone demo execution
 */
class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $dbname = 'hotel_booking_db';
            $username = 'root';
            $password = '';
            $charset = 'utf8mb4';

            try {
                // Primary: Attempt MySQL PDO connection
                $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // Secondary Fallback: Create SQLite memory/file database for standalone demo
                $dbPath = __DIR__ . '/../documents/demo_hotel.sqlite';
                $dsn = "sqlite:" . $dbPath;
                self::$instance = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                self::bootstrapSqlite(self::$instance);
            }
        }
        return self::$instance;
    }

    /**
     * Bootstraps SQLite tables & sample data if MySQL server is offline during standalone execution
     */
    private static function bootstrapSqlite(PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                phone TEXT,
                city TEXT,
                country TEXT DEFAULT 'Vietnam',
                role TEXT CHECK(role IN ('customer', 'admin')) DEFAULT 'customer',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS hotels (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                city TEXT NOT NULL,
                address TEXT NOT NULL,
                star_rating REAL DEFAULT 4.8,
                image_url TEXT,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS room_types (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                hotel_id INTEGER NOT NULL,
                type_name TEXT NOT NULL,
                base_price_per_night REAL NOT NULL,
                max_occupancy INTEGER DEFAULT 2,
                description TEXT,
                amenities TEXT,
                FOREIGN KEY(hotel_id) REFERENCES hotels(id)
            );
            CREATE TABLE IF NOT EXISTS rooms (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                room_type_id INTEGER NOT NULL,
                room_number TEXT NOT NULL,
                floor_number INTEGER DEFAULT 1,
                status TEXT DEFAULT 'available',
                FOREIGN KEY(room_type_id) REFERENCES room_types(id)
            );
            CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_code TEXT UNIQUE NOT NULL,
                user_id INTEGER NOT NULL,
                room_id INTEGER NOT NULL,
                check_in_date TEXT NOT NULL,
                check_out_date TEXT NOT NULL,
                total_guests INTEGER DEFAULT 1,
                total_amount REAL NOT NULL,
                status TEXT DEFAULT 'confirmed',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id),
                FOREIGN KEY(room_id) REFERENCES rooms(id)
            );
            CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_id INTEGER NOT NULL,
                payment_method TEXT NOT NULL,
                transaction_reference TEXT UNIQUE NOT NULL,
                amount_paid REAL NOT NULL,
                payment_status TEXT DEFAULT 'success',
                paid_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(booking_id) REFERENCES bookings(id)
            );
        ");

        // Seed default records if empty
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $pass = password_hash('password123', PASSWORD_BCRYPT);
            $pdo->exec(<<<SQL
                INSERT INTO users (full_name, email, password_hash, phone, city, country, role) VALUES 
                ('Admin System Manager', 'admin@grandvista.com', '{$pass}', '+84 901 234 567', 'Ha Noi', 'Vietnam', 'admin'),
                ('Nguyen Van Anh', 'vananh@gmail.com', '{$pass}', '+84 912 345 678', 'Da Nang', 'Vietnam', 'customer'),
                ('Elena Rostova', 'elena@travel.com', '{$pass}', '+44 7700 900', 'London', 'UK', 'customer');

                INSERT INTO hotels (id, name, city, address, star_rating, image_url, description) VALUES 
                (1, 'Grand Vista Ha Noi', 'Ha Noi', '146 Giang Vo, Ba Dinh, Ha Noi', 5.0, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'Luxury 5-star hotel in the heart of Hanoi featuring panoramic city views, infinity pool, and spa.'),
                (2, 'Grand Vista Ocean Resort Da Nang', 'Da Nang', '88 Vo Nguyen Giap, Da Nang', 4.8, 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80', 'Beachfront luxury resort with private beach access, ocean views, and fine dining.'),
                (3, 'Grand Vista Saigon Landmark', 'Ho Chi Minh City', '720A Dien Bien Phu, HCMC', 4.9, 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80', 'Skyscraper hotel featuring executive suites, rooftop lounge, and business facilities.'),
                (4, 'Asteria Bay Hotel Nha Trang', 'Nha Trang', '12 Tran Phu, Nha Trang', 4.7, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80', 'Coastal stay with ocean terrace, spa, and weekend sunset dining experience.'),
                (5, 'Azure Pearl Resort Phu Quoc', 'Phu Quoc', '88 Tran Hung Dao, Duong Dong', 5.0, 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80', 'Private island resort with long beach villas, lagoon pool, and wellness treatment rooms.'),
                (6, 'Cedar Hill Boutique Da Lat', 'Da Lat', '21 Nguyen Thong, Da Lat', 4.6, 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80', 'Cool-climate retreat surrounded by pine forests and mountain-view patios.'),
                (7, 'Emerald River Hotel Hue', 'Hue', '18 Nguyen Hoang, Hue', 4.7, 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=1200&q=80', 'Heritage-inspired riverside stay with garden terraces and royal cuisine experiences.'),
                (8, 'Sunlit Coast Resort Quy Nhon', 'Quy Nhon', '45 Tran Phu, Quy Nhon', 4.5, 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80', 'Sea-view retreat with warm coastlines, wellness spa, and barefoot dining.'),
                (9, 'Golden Bamboo Retreat Cat Ba', 'Cat Ba', '8 Cat Ba Island Road, Hai Phong', 4.8, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80', 'Island hideaway with emerald bay panoramas, rooftop lounge, and eco-luxury suites.');

                INSERT INTO room_types (id, hotel_id, type_name, base_price_per_night, max_occupancy, description, amenities) VALUES 
                (1, 1, 'Deluxe City View', 120.00, 2, '35sqm room featuring king bed, marble bathroom, and Hanoi city skyline view.', '["WiFi", "Smart TV", "Mini Bar", "AC"]'),
                (2, 1, 'Presidential Suite', 450.00, 4, '90sqm suite with private lounge, jacuzzi, and butler service.', '["WiFi", "Jacuzzi", "Butler Service", "Breakfast"]'),
                (3, 2, 'Ocean Front Villa', 280.00, 3, 'Private beachside villa with direct ocean access and plunge pool.', '["Private Pool", "Ocean View", "WiFi"]'),
                (4, 3, 'Executive Sky Suite', 210.00, 2, 'High-floor suite with Saigon river view and VIP lounge access.', '["Executive Lounge", "WiFi", "Workplace"]'),
                (5, 4, 'Garden Deluxe', 180.00, 2, 'Bright seaside room with balcony, spa access, and tropical garden view.', '["Sea View", "Balcony", "Spa Access", "WiFi"]'),
                (6, 5, 'Lagoon Family Suite', 340.00, 4, 'Large family suite with private terrace, lounge area, and lagoon pool access.', '["Pool Access", "Terrace", "Breakfast", "WiFi"]'),
                (7, 6, 'Highland Retreat', 190.00, 2, 'Mountain-style room with cozy fireplace, glass balcony, and coffee bar.', '["Glass Balcony", "Fireplace", "Coffee Bar", "WiFi"]'),
                (8, 7, 'Riverside Heritage Room', 170.00, 2, 'Historic river-facing room with courtyard bath and breakfast terrace.', '["River View", "Breakfast", "Balcony", "WiFi"]'),
                (9, 8, 'Sunset Terrace Deluxe', 200.00, 2, 'Quiet beach-front room with sunset terrace and dedicated spa access.', '["Terrace", "Ocean View", "Spa Access", "WiFi"]'),
                (10, 9, 'Bay View Suite', 260.00, 3, 'Island suite with lounge seating, panoramic bay view, and private balcony.', '["Bay View", "Private Balcony", "Breakfast", "WiFi"]');

                INSERT INTO rooms (id, room_type_id, room_number, floor_number, status) VALUES 
                (1, 1, '101', 1, 'available'),
                (2, 1, '102', 1, 'available'),
                (3, 2, '501', 5, 'available'),
                (4, 3, 'V-01', 1, 'available'),
                (5, 4, '2204', 22, 'available'),
                (6, 5, 'N-201', 2, 'available'),
                (7, 5, 'N-202', 2, 'available'),
                (8, 6, 'PQ-1101', 11, 'available'),
                (9, 7, 'DL-308', 3, 'available'),
                (10, 8, 'H-214', 2, 'available'),
                (11, 9, 'QN-305', 3, 'available'),
                (12, 10, 'CB-401', 4, 'available');

                INSERT INTO bookings (id, booking_code, user_id, room_id, check_in_date, check_out_date, total_guests, total_amount, status) VALUES 
                (1, 'GVB-2026-8801', 2, 1, '2026-08-10', '2026-08-13', 2, 360.00, 'confirmed'),
                (2, 'GVB-2026-8802', 3, 3, '2026-08-15', '2026-08-18', 2, 1350.00, 'confirmed');

                INSERT INTO payments (id, booking_id, payment_method, transaction_reference, amount_paid, payment_status) VALUES 
                (1, 1, 'credit_card', 'PAY-VISA-99281', 360.00, 'success'),
                (2, 2, 'e_wallet', 'PAY-MOMO-88210', 1350.00, 'success');
SQL);
        }

        $hotelCount = (int)$pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
        if ($hotelCount < 9) {
            $pdo->exec(<<<SQL
                INSERT OR IGNORE INTO hotels (id, name, city, address, star_rating, image_url, description) VALUES 
                (4, 'Asteria Bay Hotel Nha Trang', 'Nha Trang', '12 Tran Phu, Nha Trang', 4.7, 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80', 'Coastal stay with ocean terrace, spa, and weekend sunset dining experience.'),
                (5, 'Azure Pearl Resort Phu Quoc', 'Phu Quoc', '88 Tran Hung Dao, Duong Dong', 5.0, 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80', 'Private island resort with long beach villas, lagoon pool, and wellness treatment rooms.'),
                (6, 'Cedar Hill Boutique Da Lat', 'Da Lat', '21 Nguyen Thong, Da Lat', 4.6, 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80', 'Cool-climate retreat surrounded by pine forests and mountain-view patios.'),
                (7, 'Emerald River Hotel Hue', 'Hue', '18 Nguyen Hoang, Hue', 4.7, 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=1200&q=80', 'Heritage-inspired riverside stay with garden terraces and royal cuisine experiences.'),
                (8, 'Sunlit Coast Resort Quy Nhon', 'Quy Nhon', '45 Tran Phu, Quy Nhon', 4.5, 'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80', 'Sea-view retreat with warm coastlines, wellness spa, and barefoot dining.'),
                (9, 'Golden Bamboo Retreat Cat Ba', 'Cat Ba', '8 Cat Ba Island Road, Hai Phong', 4.8, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80', 'Island hideaway with emerald bay panoramas, rooftop lounge, and eco-luxury suites.');

                INSERT OR IGNORE INTO room_types (id, hotel_id, type_name, base_price_per_night, max_occupancy, description, amenities) VALUES 
                (5, 4, 'Garden Deluxe', 180.00, 2, 'Bright seaside room with balcony, spa access, and tropical garden view.', '["Sea View", "Balcony", "Spa Access", "WiFi"]'),
                (6, 5, 'Lagoon Family Suite', 340.00, 4, 'Large family suite with private terrace, lounge area, and lagoon pool access.', '["Pool Access", "Terrace", "Breakfast", "WiFi"]'),
                (7, 6, 'Highland Retreat', 190.00, 2, 'Mountain-style room with cozy fireplace, glass balcony, and coffee bar.', '["Glass Balcony", "Fireplace", "Coffee Bar", "WiFi"]'),
                (8, 7, 'Riverside Heritage Room', 170.00, 2, 'Historic river-facing room with courtyard bath and breakfast terrace.', '["River View", "Breakfast", "Balcony", "WiFi"]'),
                (9, 8, 'Sunset Terrace Deluxe', 200.00, 2, 'Quiet beach-front room with sunset terrace and dedicated spa access.', '["Terrace", "Ocean View", "Spa Access", "WiFi"]'),
                (10, 9, 'Bay View Suite', 260.00, 3, 'Island suite with lounge seating, panoramic bay view, and private balcony.', '["Bay View", "Private Balcony", "Breakfast", "WiFi"]');

                INSERT OR IGNORE INTO rooms (id, room_type_id, room_number, floor_number, status) VALUES 
                (6, 5, 'N-201', 2, 'available'),
                (7, 5, 'N-202', 2, 'available'),
                (8, 6, 'PQ-1101', 11, 'available'),
                (9, 7, 'DL-308', 3, 'available'),
                (10, 8, 'H-214', 2, 'available'),
                (11, 9, 'QN-305', 3, 'available'),
                (12, 10, 'CB-401', 4, 'available');
SQL);
        }
    }
}
