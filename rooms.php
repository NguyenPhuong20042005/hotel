<?php
include __DIR__ . '/header.php';

$city = $filters['city'] ?? '';
$checkIn = $filters['check_in'] ?? date('Y-m-d');
$checkOut = $filters['check_out'] ?? date('Y-m-d', strtotime('+3 days'));
$guests = (int)($filters['guests'] ?? 1);
?>

<main class="container">
  <div class="search-card" style="margin-bottom: 3rem;">
    <form action="/rooms" method="GET" class="search-form">
      <div class="form-group">
        <label>Destination City</label>
        <select name="city" class="form-control">
          <option value="">All Locations</option>
          <option value="Ha Noi" <?= $city === 'Ha Noi' ? 'selected' : '' ?>>Ha Noi</option>
          <option value="Da Nang" <?= $city === 'Da Nang' ? 'selected' : '' ?>>Da Nang</option>
          <option value="Ho Chi Minh City" <?= $city === 'Ho Chi Minh City' ? 'selected' : '' ?>>Ho Chi Minh City</option>
          <option value="Nha Trang" <?= $city === 'Nha Trang' ? 'selected' : '' ?>>Nha Trang</option>
          <option value="Phu Quoc" <?= $city === 'Phu Quoc' ? 'selected' : '' ?>>Phu Quoc</option>
          <option value="Da Lat" <?= $city === 'Da Lat' ? 'selected' : '' ?>>Da Lat</option>
          <option value="Hue" <?= $city === 'Hue' ? 'selected' : '' ?>>Hue</option>
          <option value="Quy Nhon" <?= $city === 'Quy Nhon' ? 'selected' : '' ?>>Quy Nhon</option>
          <option value="Cat Ba" <?= $city === 'Cat Ba' ? 'selected' : '' ?>>Cat Ba</option>
        </select>
      </div>

      <div class="form-group">
        <label>Check In</label>
        <input type="date" name="check_in" class="form-control" value="<?= e($checkIn) ?>">
      </div>

      <div class="form-group">
        <label>Check Out</label>
        <input type="date" name="check_out" class="form-control" value="<?= e($checkOut) ?>">
      </div>

      <div class="form-group">
        <label>Guests</label>
        <select name="guests" class="form-control">
          <option value="1" <?= $guests == 1 ? 'selected' : '' ?>>1 Guest</option>
          <option value="2" <?= $guests == 2 ? 'selected' : '' ?>>2 Guests</option>
          <option value="3" <?= $guests == 3 ? 'selected' : '' ?>>3 Guests</option>
          <option value="4" <?= $guests == 4 ? 'selected' : '' ?>>4+ Guests</option>
        </select>
      </div>

      <button type="submit" class="btn-accent" style="height: 48px; justify-content: center;">Filter Rooms</button>
    </form>
  </div>

  <div class="section-header">
    <div>
      <h2 class="section-title">Available Accommodations</h2>
      <p style="color: var(--text-muted);">Showing live room inventory for your selected dates</p>
    </div>
  </div>

  <?php if (empty($rooms)): ?>
    <div style="text-align: center; padding: 4rem 2rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
      <i class="fa-solid fa-bed" style="font-size: 3rem; color: var(--text-dim); margin-bottom: 1rem;"></i>
      <h3>No Rooms Found Matching Criteria</h3>
      <p style="color: var(--text-muted); margin-top: 0.5rem;">Try adjusting your dates or location filter.</p>
    </div>
  <?php else: ?>
    <div class="grid-3">
      <?php foreach ($rooms as $room): ?>
        <div class="card">
          <div class="card-img-wrap">
            <img src="<?= e($room['image_url']) ?>" alt="<?= e($room['type_name']) ?>" class="card-img">
            <div class="badge-tag"><i class="fa-solid fa-star"></i> <?= e($room['star_rating']) ?></div>
          </div>
          <div class="card-body">
            <span style="font-size: 0.8rem; text-transform: uppercase; color: var(--accent-gold); font-weight: 600;"><?= e($room['hotel_name']) ?></span>
            <h3 class="card-title"><?= e($room['type_name']) ?></h3>
            <p class="card-sub"><i class="fa-solid fa-location-dot"></i> <?= e($room['city']) ?></p>

            <div class="card-price">
              <?= formatCurrency($room['base_price_per_night']) ?> <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 400;">/ night</span>
            </div>

            <div class="amenities-pills">
              <span class="pill"><i class="fa-solid fa-user"></i> Max <?= e($room['max_occupancy']) ?> Guests</span>
              <?php foreach ($room['amenities_list'] as $amenity): ?>
                <span class="pill"><i class="fa-solid fa-check" style="color: var(--accent-gold);"></i> <?= e($amenity) ?></span>
              <?php endforeach; ?>
            </div>

            <div style="margin-top: auto;">
              <?php if ($room['is_available']): ?>
                <a href="/booking?room_type_id=<?= $room['room_type_id'] ?>&room_id=<?= $room['available_room_id'] ?>&check_in=<?= e($checkIn) ?>&check_out=<?= e($checkOut) ?>&guests=<?= e($guests) ?>" class="btn-accent" style="width: 100%; text-align: center; justify-content: center;">
                  <i class="fa-solid fa-bolt"></i> Book Now (Live Available)
                </a>
              <?php else: ?>
                <button class="btn-outline" disabled style="width: 100%; opacity: 0.5; cursor: not-allowed;">
                  <i class="fa-solid fa-lock"></i> Fully Reserved
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/footer.php'; ?>
