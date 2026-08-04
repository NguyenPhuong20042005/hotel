<?php
require_once __DIR__ . '/../config/app.php';
$authUser = getAuthUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(APP_NAME) ?> - Centralized Booking System</title>
  <link rel="stylesheet" href="/public/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
  <a href="/" class="brand-logo">
    <i class="fa-solid fa-hotel" style="color: var(--accent-gold);"></i>
    <span>Grand Vista</span> Chains
  </a>

  <ul class="nav-links">
    <li><a href="/" class="nav-link">Home</a></li>
    <li><a href="/rooms" class="nav-link">Browse Rooms</a></li>
    <?php if ($authUser): ?>
      <li><a href="/profile" class="nav-link"><i class="fa-solid fa-user-circle"></i> My Bookings</a></li>
      <?php if (isAdmin()): ?>
        <li><a href="/admin" class="nav-link" style="color: var(--accent-gold);"><i class="fa-solid fa-chart-line"></i> Admin Dashboard</a></li>
      <?php endif; ?>
      <li><a href="/logout" class="btn-outline" style="padding: 0.4rem 0.9rem; font-size: 0.85rem;">Logout</a></li>
    <?php else: ?>
      <li><a href="/login" class="nav-link">Sign In</a></li>
      <li><a href="/register" class="btn-accent">Register Account</a></li>
    <?php endif; ?>
  </ul>
</nav>

<?php if ($flash): ?>
  <div style="width: 90%; max-width: 1280px; margin: 1rem auto -1rem auto; padding: 1rem 1.5rem; border-radius: var(--radius-md); background: <?= $flash['type'] === 'error' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(16, 185, 129, 0.2)' ?>; border: 1px solid <?= $flash['type'] === 'error' ? '#f87171' : '#34d399' ?>; color: #fff;">
    <?= e($flash['message']) ?>
  </div>
<?php endif; ?>
