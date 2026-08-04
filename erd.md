# Entity Relationship Diagram (ERD) - Hotel Room Booking System

This document describes the relational database structure for the Hotel Room Booking System using Mermaid ERD notation.

## Mermaid ERD Diagram

```mermaid
erDiagram
    USERS ||--o{ BOOKINGS : "places"
    HOTELS ||--|{ ROOM_TYPES : "offers"
    ROOM_TYPES ||--|{ ROOMS : "contains"
    ROOMS ||--o{ BOOKINGS : "is_reserved_in"
    BOOKINGS ||--|| PAYMENTS : "generates"
    USERS ||--o{ REVIEWS : "writes"
    HOTELS ||--o{ REVIEWS : "receives"

    USERS {
        int id PK
        string full_name
        string email UK
        string password_hash
        string phone
        string city
        string country
        enum role "customer, admin"
        datetime created_at
    }

    HOTELS {
        int id PK
        string name
        string city
        string address
        decimal star_rating
        string image_url
        text description
        datetime created_at
    }

    ROOM_TYPES {
        int id PK
        int hotel_id FK
        string type_name "Deluxe, Suite, Executive"
        decimal base_price_per_night
        int max_occupancy
        text description
        json amenities
    }

    ROOMS {
        int id PK
        int room_type_id FK
        string room_number
        int floor_number
        enum status "available, maintenance, occupied"
    }

    BOOKINGS {
        int id PK
        string booking_code UK
        int user_id FK
        int room_id FK
        date check_in_date
        date check_out_date
        int total_guests
        decimal total_amount
        enum status "pending, confirmed, cancelled, completed"
        datetime created_at
    }

    PAYMENTS {
        int id PK
        int booking_id FK
        enum payment_method "credit_card, e_wallet, bank_transfer"
        string transaction_reference UK
        decimal amount_paid
        enum payment_status "pending, success, failed, refunded"
        datetime paid_at
    }

    REVIEWS {
        int id PK
        int user_id FK
        int hotel_id FK
        int rating "1-5 Stars"
        text comment
        datetime created_at
    }
```

## Relational Constraints & Keys
1. `HOTELS` (1) -> `ROOM_TYPES` (N): A hotel branch offers multiple room types.
2. `ROOM_TYPES` (1) -> `ROOMS` (N): A room type categorizes specific physical room numbers.
3. `USERS` (1) -> `BOOKINGS` (N): A registered customer can create multiple room reservations over time.
4. `ROOMS` (1) -> `BOOKINGS` (N): A specific room can have multiple non-overlapping booking date ranges.
5. `BOOKINGS` (1) -> `PAYMENTS` (1): Each booking has a corresponding payment record.
6. `USERS` (1) -> `REVIEWS` (N) & `HOTELS` (1) -> `REVIEWS` (N): Customers can review hotel stays.
