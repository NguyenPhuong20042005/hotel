<?php include __DIR__ . '/header.php'; ?>

<main class="container">
  <div style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem;">
    
    <!-- Profile Card -->
    <div class="card" style="padding: 1.75rem; height: fit-content;">
      <div style="text-align: center; margin-bottom: 1.5rem;">
        <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--accent-gradient); color: #000; font-size: 2rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
          <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
        </div>
        <h3 style="font-size: 1.2rem;"><?= e($user['full_name']) ?></h3>
        <p style="color: var(--text-muted); font-size: 0.85rem;"><?= e($user['email']) ?></p>
        <span class="badge-tag" style="position: static; display: inline-block; margin-top: 0.75rem;"><?= ucfirst($user['role']) ?> Account</span>
      </div>

      <form action="/profile/update" method="POST" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        <div class="form-group" style="margin-bottom: 1rem;">
          <label>Full Name</label>
          <input type="text" name="full_name" class="form-control" value="<?= e($user['full_name']) ?>" required>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
          <label>Phone Number</label>
          <input type="text" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
          <label>City</label>
          <input type="text" name="city" class="form-control" value="<?= e($user['city'] ?? '') ?>">
        </div>

        <button type="submit" class="btn-outline" style="width: 100%;">Update Info</button>
      </form>
    </div>

    <!-- Booking History -->
    <div>
      <div class="section-header" style="margin-bottom: 1.5rem;">
        <div>
          <h2 class="section-title">Booking History</h2>
          <p style="color: var(--text-muted);">Manage your current and past hotel reservations</p>
        </div>
      </div>

      <?php if (empty($bookings)): ?>
        <div class="card" style="padding: 3rem; text-align: center;">
          <p style="color: var(--text-muted);">You have no reservations yet.</p>
          <a href="/rooms" class="btn-accent" style="margin-top: 1rem; display: inline-block;">Browse Available Rooms</a>
        </div>
      <?php else: ?>
        <div class="table-card">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Code</th>
                <th>Hotel & Room</th>
                <th>Dates</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $b): ?>
                <tr>
                  <td><strong style="color: var(--accent-gold);"><?= e($b['booking_code']) ?></strong></td>
                  <td>
                    <div><strong><?= e($b['hotel_name']) ?></strong></div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);"><?= e($b['type_name']) ?> (Room <?= e($b['room_number']) ?>)</div>
                  </td>
                  <td style="font-size: 0.85rem;">
                    <?= e($b['check_in_date']) ?> <br> to <?= e($b['check_out_date']) ?>
                  </td>
                  <td><strong><?= formatCurrency($b['total_amount']) ?></strong></td>
                  <td>
                    <span class="status-badge status-<?= e($b['status']) ?>">
                      <?= ucfirst($b['status']) ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($b['status'] === 'pending'): ?>
                      <a href="/checkout?booking_id=<?= $b['id'] ?>" class="btn-accent" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Pay Now</a>
                    <?php elseif (in_array($b['status'], ['confirmed', 'completed'])): ?>
                      <a href="/invoice?booking_id=<?= $b['id'] ?>" class="btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" target="_blank"><i class="fa-solid fa-receipt"></i> Invoice</a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
