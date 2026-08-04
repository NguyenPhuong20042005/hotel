<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Hotel.php';
require_once __DIR__ . '/models/Booking.php';
require_once __DIR__ . '/models/Payment.php';
require_once __DIR__ . '/models/Report.php';

// Parse Request URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Standardize route path
$route = rtrim($requestUri, '/');
if (empty($route)) {
    $route = '/';
}

// ROUTING MATRIX
switch ($route) {
    // -------------------------------------------------------------
    // HOME & ROOM BROWSING
    // -------------------------------------------------------------
    case '/':
        $hotelModel = new Hotel();
        $hotels = $hotelModel->getAllHotels();
        include __DIR__ . '/views/home.php';
        break;

    case '/rooms':
        $hotelModel = new Hotel();
        $filters = [
            'city' => $_GET['city'] ?? '',
            'check_in' => $_GET['check_in'] ?? date('Y-m-d'),
            'check_out' => $_GET['check_out'] ?? date('Y-m-d', strtotime('+3 days')),
            'guests' => (int)($_GET['guests'] ?? 1)
        ];
        $rooms = $hotelModel->searchAvailableRooms(
            $filters['city'],
            $filters['check_in'],
            $filters['check_out'],
            $filters['guests']
        );
        include __DIR__ . '/views/rooms.php';
        break;

    // -------------------------------------------------------------
    // BOOKING WORKFLOW
    // -------------------------------------------------------------
    case '/booking':
        $roomTypeId = (int)($_GET['room_type_id'] ?? 0);
        $roomId = (int)($_GET['room_id'] ?? 0);
        $checkIn = $_GET['check_in'] ?? date('Y-m-d');
        $checkOut = $_GET['check_out'] ?? date('Y-m-d', strtotime('+3 days'));
        $guests = (int)($_GET['guests'] ?? 2);

        $hotelModel = new Hotel();
        $db = Database::getConnection();

        $rtStmt = $db->prepare("SELECT * FROM room_types WHERE id = ?");
        $rtStmt->execute([$roomTypeId]);
        $roomType = $rtStmt->fetch();

        if (!$roomType) {
            setFlash('error', 'Selected room type is invalid.');
            header('Location: /rooms');
            exit;
        }

        $hotel = $hotelModel->getHotelById((int)$roomType['hotel_id']);
        include __DIR__ . '/views/booking.php';
        break;

    case '/booking/confirm':
        if ($requestMethod === 'POST') {
            $user = getAuthUser();
            if (!$user) {
                setFlash('error', 'Please log in to complete your room booking.');
                header('Location: /login');
                exit;
            }

            $roomId = (int)($_POST['room_id'] ?? 0);
            $checkIn = $_POST['check_in'] ?? '';
            $checkOut = $_POST['check_out'] ?? '';
            $guests = (int)($_POST['guests'] ?? 1);
            $totalAmount = (float)($_POST['total_amount'] ?? 0);

            $bookingModel = new Booking();
            $booking = $bookingModel->createBooking($user['id'], $roomId, $checkIn, $checkOut, $guests, $totalAmount);

            if ($booking) {
                setFlash('success', 'Booking created successfully! Complete payment to confirm your room.');
                header("Location: /checkout?booking_id={$booking['id']}");
                exit;
            } else {
                setFlash('error', 'Selected room is no longer available for your dates.');
                header('Location: /rooms');
                exit;
            }
        }
        break;

    // -------------------------------------------------------------
    // PAYMENT WORKFLOW
    // -------------------------------------------------------------
    case '/checkout':
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingById($bookingId);

        if (!$booking) {
            setFlash('error', 'Booking record not found.');
            header('Location: /rooms');
            exit;
        }
        include __DIR__ . '/views/checkout.php';
        break;

    case '/checkout/process':
        if ($requestMethod === 'POST') {
            $bookingId = (int)($_POST['booking_id'] ?? 0);
            $method = $_POST['payment_method'] ?? 'credit_card';
            $amount = (float)($_POST['amount'] ?? 0);

            $paymentModel = new Payment();
            $result = $paymentModel->processPayment($bookingId, $method, $amount);

            if ($result) {
                setFlash('success', "Payment successful! Transaction Ref: {$result['transaction_ref']}");
                header('Location: /profile');
                exit;
            } else {
                setFlash('error', 'Payment processing failed. Please retry.');
                header("Location: /checkout?booking_id={$bookingId}");
                exit;
            }
        }
        break;

    // -------------------------------------------------------------
    // CUSTOMER PROFILE & ADMIN REPORTING
    // -------------------------------------------------------------
    case '/profile':
        $user = getAuthUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $bookingModel = new Booking();
        $bookings = $bookingModel->getBookingsByUserId($user['id']);
        include __DIR__ . '/views/profile.php';
        break;

    case '/profile/update':
        if ($requestMethod === 'POST') {
            $user = getAuthUser();
            if ($user) {
                $userModel = new User();
                $userModel->updateProfile($user['id'], $_POST['full_name'], $_POST['phone'], $_POST['city']);
                $_SESSION['user']['full_name'] = $_POST['full_name'];
                $_SESSION['user']['phone'] = $_POST['phone'];
                $_SESSION['user']['city'] = $_POST['city'];
                setFlash('success', 'Profile updated successfully.');
            }
            header('Location: /profile');
            exit;
        }
        break;

    case '/admin':
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Administrator privilege required.');
            header('Location: /login');
            exit;
        }

        $reportModel = new Report();
        $occupancy = $reportModel->getOccupancyRate();
        $revenue = $reportModel->getRevenueMetrics();
        $demographics = $reportModel->getDemographics();

        include __DIR__ . '/views/admin.php';
        break;

    // -------------------------------------------------------------
    // AUTHENTICATION
    // -------------------------------------------------------------
    case '/login':
        include __DIR__ . '/views/login.php';
        break;

    case '/login/submit':
        if ($requestMethod === 'POST') {
            $userModel = new User();
            $user = $userModel->login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($user) {
                $_SESSION['user'] = $user;
                setFlash('success', "Welcome back, {$user['full_name']}!");
                header('Location: ' . ($user['role'] === 'admin' ? '/admin' : '/profile'));
                exit;
            } else {
                setFlash('error', 'Invalid email or password credentials.');
                header('Location: /login');
                exit;
            }
        }
        break;

    case '/register':
        include __DIR__ . '/views/register.php';
        break;

    case '/register/submit':
        if ($requestMethod === 'POST') {
            $userModel = new User();
            $user = $userModel->register(
                $_POST['full_name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['phone'] ?? '',
                $_POST['city'] ?? ''
            );

            if ($user) {
                $_SESSION['user'] = $user;
                setFlash('success', 'Account registered successfully!');
                header('Location: /profile');
                exit;
            } else {
                setFlash('error', 'Email address is already registered.');
                header('Location: /register');
                exit;
            }
        }
        break;

    case '/logout':
        unset($_SESSION['user']);
        session_destroy();
        header('Location: /');
        exit;

    default:
        http_response_code(404);
        echo "<h1 style='font-family: sans-serif; text-align: center; margin-top: 5rem;'>404 - Page Not Found</h1>";
        break;
}
