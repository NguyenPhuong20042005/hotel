# Use Case Diagram - Hotel Room Booking System

This document outlines the Use Case Diagram for the centralized Hotel Room Booking System using Mermaid syntax.

## Mermaid Diagram

```mermaid
usecaseDiagram
    actor "Customer" as Customer
    actor "Hotel Staff / Admin" as Admin
    actor "Payment Gateway System" as PaymentGateway

    package "Hotel Room Booking System" {
        usecase "UC-01: Register / Login Account" as UC1
        usecase "UC-02: Manage Personal Profile" as UC2
        usecase "UC-03: Search & Filter Rooms" as UC3
        usecase "UC-04: Check Real-time Availability" as UC4
        usecase "UC-05: Create Room Booking" as UC5
        usecase "UC-06: Process Payment" as UC6
        usecase "UC-07: Pay via Credit Card" as UC6a
        usecase "UC-08: Pay via E-Wallet" as UC6b
        usecase "UC-09: Pay via Bank Transfer" as UC6c
        usecase "UC-10: View Booking History & Invoices" as UC7
        usecase "UC-11: Cancel Booking" as UC8
        usecase "UC-12: Manage Hotel & Room Inventory" as UC9
        usecase "UC-13: Manage All Bookings" as UC10
        usecase "UC-14: Generate Occupancy Reports" as UC11
        usecase "UC-15: Generate Revenue & Demographic Analytics" as UC12
    }

    Customer --> UC1
    Customer --> UC2
    Customer --> UC3
    Customer --> UC5
    Customer --> UC7
    Customer --> UC8

    UC3 ..> UC4 : <<include>>
    UC5 ..> UC6 : <<include>>

    UC6 <|-- UC6a
    UC6 <|-- UC6b
    UC6 <|-- UC6c

    PaymentGateway <-- UC6a
    PaymentGateway <-- UC6b
    PaymentGateway <-- UC6c

    Admin --> UC1
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
```

## Actor Descriptions
1. **Customer**: Searches for rooms across hotel locations, checks real-time availability, creates bookings, makes payments, and views personal booking history.
2. **Hotel Staff / Admin**: Manages hotel branches, room types, pricing, monitors live bookings, and generates business reporting (occupancy rates, revenue trends, demographics).
3. **Payment Gateway System**: External payment providers (Credit Card Processor, E-Wallet API, VietQR/SEPA Gateway) handling payment authorization and status callbacks.
