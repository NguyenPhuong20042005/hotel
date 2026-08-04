<?php include __DIR__ . '/header.php'; ?>

<main class="container">
  <div style="max-width: 750px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 2.5rem;">
      <span class="status-badge status-pending"><i class="fa-solid fa-clock"></i> Reservation Pending Payment</span>
      <h2 class="section-title" style="margin-top: 0.75rem;">Payment Gateway Integration</h2>
      <p style="color: var(--text-muted);">Booking Reference: <strong style="color: var(--accent-gold);"><?= e($booking['booking_code']) ?></strong></p>
    </div>

    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(212, 175, 55, 0.08); padding: 1.2rem; border-radius: var(--radius-md); border: 1px solid var(--border-glow); margin-bottom: 2rem;">
        <div>
          <div style="font-size: 0.85rem; color: var(--text-muted);">Total Amount Due</div>
          <div style="font-size: 1.75rem; font-weight: 700; color: var(--accent-gold);"><?= formatCurrency($booking['total_amount']) ?></div>
        </div>
        <div style="text-align: right; font-size: 0.9rem;">
          <div><strong><?= e($booking['hotel_name']) ?></strong></div>
          <div style="color: var(--text-muted);"><?= e($booking['type_name']) ?> (Room <?= e($booking['room_number']) ?>)</div>
        </div>
      </div>

      <!-- Payment Method Tabs -->
      <label style="font-size: 0.9rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.75rem;">Select Payment Gateway</label>
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
        <button type="button" class="pay-method-tab active btn-outline" data-method="credit_card" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem;">
          <i class="fa-solid fa-credit-card" style="font-size: 1.5rem; color: var(--accent-gold);"></i>
          <span>Credit / Debit Card</span>
        </button>
        <button type="button" class="pay-method-tab btn-outline" data-method="e_wallet" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem;">
          <i class="fa-solid fa-wallet" style="font-size: 1.5rem; color: var(--accent-purple);"></i>
          <span>E-Wallet (MoMo)</span>
        </button>
        <button type="button" class="pay-method-tab btn-outline" data-method="bank_transfer" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem;">
          <i class="fa-solid fa-qrcode" style="font-size: 1.5rem; color: var(--accent-emerald);"></i>
          <span>Bank Transfer (QR)</span>
        </button>
      </div>

      <form action="/checkout/process" method="POST">
        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
        <input type="hidden" id="selected_payment_method" name="payment_method" value="credit_card">
        <input type="hidden" name="amount" value="<?= $booking['total_amount'] ?>">

        <!-- Details Panel 1: Credit Card -->
        <div id="panel_credit_card" class="pay-details-panel">
          <div class="form-group" style="margin-bottom: 1rem;">
            <label>Cardholder Name</label>
            <input type="text" class="form-control" placeholder="e.g. NGUYEN VAN ANH" value="<?= e($booking['customer_name']) ?>">
          </div>
          <div class="form-group" style="margin-bottom: 1rem;">
            <label>Card Number</label>
            <input type="text" class="form-control" placeholder="4532 •••• •••• 8892">
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div class="form-group">
              <label>Expiry Date</label>
              <input type="text" class="form-control" placeholder="MM/YY">
            </div>
            <div class="form-group">
              <label>CVV Code</label>
              <input type="password" class="form-control" placeholder="•••">
            </div>
          </div>
        </div>

        <!-- Details Panel 2: E-Wallet -->
        <div id="panel_e_wallet" class="pay-details-panel" style="display: none; text-align: center; padding: 1.5rem; background: rgba(139, 92, 246, 0.08); border-radius: var(--radius-md); margin-bottom: 1.5rem;">
          <i class="fa-solid fa-mobile-screen-button" style="font-size: 2.5rem; color: var(--accent-purple); margin-bottom: 0.75rem;"></i>
          <h4 style="margin-bottom: 0.5rem;">MoMo / ZaloPay / PayPal</h4>
          <p style="color: var(--text-muted); font-size: 0.9rem;">You will authorize instant payout of <?= formatCurrency($booking['total_amount']) ?> via your linked mobile app.</p>
        </div>

        <!-- Details Panel 3: Bank Transfer QR -->
        <div id="panel_bank_transfer" class="pay-details-panel" style="display: none; text-align: center; padding: 1.5rem; background: rgba(16, 185, 129, 0.08); border-radius: var(--radius-md); margin-bottom: 1.5rem;">
          <div style="width: 140px; height: 140px; background: #fff; margin: 0 auto 1rem auto; padding: 10px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: #000; font-weight: 700; font-size: 0.8rem;">
            [VietQR Simulated Code]
          </div>
          <h4>Grand Vista Hotel Group Account</h4>
          <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">Bank: Vietcombank | Acc No: 9988221008</p>
        </div>

        <button type="submit" class="btn-accent" style="width: 100%; height: 50px; justify-content: center; font-size: 1.05rem;">
          <i class="fa-solid fa-shield-halved"></i> Authorize & Confirm Booking (<?= formatCurrency($booking['total_amount']) ?>)
        </button>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
