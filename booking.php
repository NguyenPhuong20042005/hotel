<?php
include __DIR__ . '/header.php';

$hotel = $hotel ?? [
  'image_url' => '',
  'name' => 'Selected Hotel',
  'address' => 'Hotel address unavailable'
];
$roomType = $roomType ?? [
  'type_name' => 'Selected Room',
  'base_price_per_night' => 0,
  'max_occupancy' => 1,
];
$roomId = (int)($roomId ?? 0);
$checkIn = $checkIn ?? date('Y-m-d');
$checkOut = $checkOut ?? date('Y-m-d', strtotime('+3 days'));
$guests = (int)($guests ?? 1);
?>

<main class="container">
  <div style="max-width: 800px; margin: 0 auto;">
    <h2 class="section-title" style="margin-bottom: 0.5rem;">Complete Your Reservation</h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">Verify your stay details and confirm your room booking.</p>

    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
      <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
        <img src="<?= e($hotel['image_url']) ?>" alt="Hotel" style="width: 120px; height: 90px; object-fit: cover; border-radius: var(--radius-md);">
        <div>
          <span style="color: var(--accent-gold); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;"><?= e($hotel['name']) ?></span>
          <h3 style="font-size: 1.4rem; margin-top: 0.2rem;"><?= e($roomType['type_name']) ?></h3>
          <p style="color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-location-dot"></i> <?= e($hotel['address']) ?></p>
        </div>
      </div>

      <form action="/booking/confirm" method="POST">
        <input type="hidden" name="room_id" value="<?= e($roomId) ?>">
        <input type="hidden" id="input_total_amount" name="total_amount" value="">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
          <div class="form-group">
            <label><i class="fa-solid fa-calendar-days"></i> Check In Date</label>
            <input type="date" id="calc_check_in" name="check_in" class="form-control" value="<?= e($checkIn) ?>" required>
          </div>
          <div class="form-group">
            <label><i class="fa-solid fa-calendar-check"></i> Check Out Date</label>
            <input type="date" id="calc_check_out" name="check_out" class="form-control" value="<?= e($checkOut) ?>" required>
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
          <label><i class="fa-solid fa-user-group"></i> Total Guests</label>
          <select name="guests" class="form-control">
            <?php for ($i = 1; $i <= $roomType['max_occupancy']; $i++): ?>
              <option value="<?= $i ?>" <?= $i == $guests ? 'selected' : '' ?>><?= $i ?> Person(s)</option>
            <?php endfor; ?>
          </select>
        </div>

        <!-- Calculation Box -->
        <div style="background: rgba(10, 13, 20, 0.6); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 2rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
            <span style="color: var(--text-muted);">Rate per night:</span>
            <span id="base_price_val" data-price="<?= $roomType['base_price_per_night'] ?>"><?= formatCurrency($roomType['base_price_per_night']) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem;">
            <span style="color: var(--text-muted);">Duration:</span>
            <span id="total_nights_val" style="color: var(--accent-gold); font-weight: 600;">Calculating...</span>
          </div>
          <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.75rem; font-size: 1.25rem; font-weight: 700;">
            <span>Total Payable:</span>
            <span id="total_price_val" style="color: var(--accent-gold);">$0.00</span>
          </div>
        </div>

        <button type="submit" id="btn_submit_booking" class="btn-accent" style="width: 100%; height: 50px; justify-content: center; font-size: 1.05rem;">
          Proceed to Secure Payment <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
