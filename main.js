// Frontend Logic for Hotel Room Booking System

document.addEventListener('DOMContentLoaded', () => {
  initBookingCalculator();
  initPaymentGatewayModal();
  initAdminCharts();
});

/**
 * Booking Price Calculator
 */
function initBookingCalculator() {
  const checkInInput = document.getElementById('calc_check_in');
  const checkOutInput = document.getElementById('calc_check_out');
  const pricePerNightEl = document.getElementById('base_price_val');
  const totalNightsEl = document.getElementById('total_nights_val');
  const totalPriceEl = document.getElementById('total_price_val');
  const submitBtn = document.getElementById('btn_submit_booking');

  if (!checkInInput || !checkOutInput || !pricePerNightEl || !totalPriceEl) return;

  function calculate() {
    const checkIn = new Date(checkInInput.value);
    const checkOut = new Date(checkOutInput.value);
    const basePrice = parseFloat(pricePerNightEl.dataset.price || 0);

    if (checkIn && checkOut && checkOut > checkIn) {
      const diffTime = Math.abs(checkOut - checkIn);
      const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      const total = nights * basePrice;

      if (totalNightsEl) totalNightsEl.textContent = `${nights} night(s)`;
      totalPriceEl.textContent = `$${total.toFixed(2)}`;
      if (document.getElementById('input_total_amount')) {
        document.getElementById('input_total_amount').value = total.toFixed(2);
      }
      if (submitBtn) submitBtn.disabled = false;
    } else {
      if (totalNightsEl) totalNightsEl.textContent = 'Select valid dates';
      totalPriceEl.textContent = '$0.00';
      if (submitBtn) submitBtn.disabled = true;
    }
  }

  checkInInput.addEventListener('change', calculate);
  checkOutInput.addEventListener('change', calculate);
  calculate();
}

/**
 * Multi-Method Payment Gateway Selector
 */
function initPaymentGatewayModal() {
  const methodTabs = document.querySelectorAll('.pay-method-tab');
  const methodInput = document.getElementById('selected_payment_method');
  const detailsPanels = document.querySelectorAll('.pay-details-panel');

  if (!methodTabs.length || !methodInput) return;

  methodTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      methodTabs.forEach(t => t.classList.remove('active'));
      detailsPanels.forEach(p => p.style.display = 'none');

      tab.classList.add('active');
      const selected = tab.dataset.method;
      methodInput.value = selected;

      const activePanel = document.getElementById(`panel_${selected}`);
      if (activePanel) activePanel.style.display = 'block';
    });
  });
}

/**
 * Admin Reporting Visualizer (Renders HTML5 Canvas Bar Charts)
 */
function initAdminCharts() {
  const canvas = document.getElementById('revenueChartCanvas');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  const months = ['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
  const values = [12400, 18900, 24500, 31200, 28400, 34500];

  const maxVal = Math.max(...values);
  const chartHeight = canvas.height - 40;
  const barWidth = 40;
  const gap = 30;

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  values.forEach((val, idx) => {
    const x = 40 + idx * (barWidth + gap);
    const barH = (val / maxVal) * (chartHeight - 30);
    const y = canvas.height - 30 - barH;

    // Draw Gradient Bar
    const gradient = ctx.createLinearGradient(x, y, x, y + barH);
    gradient.addColorStop(0, '#d4af37');
    gradient.addColorStop(1, '#aa771c');

    ctx.fillStyle = gradient;
    ctx.beginPath();
    ctx.roundRect(x, y, barWidth, barH, [6, 6, 0, 0]);
    ctx.fill();

    // Draw Month Label
    ctx.fillStyle = '#94a3b8';
    ctx.font = '12px Outfit';
    ctx.textAlign = 'center';
    ctx.fillText(months[idx], x + barWidth / 2, canvas.height - 10);

    // Draw Value Label
    ctx.fillStyle = '#f8fafc';
    ctx.font = '11px Outfit';
    ctx.fillText(`$${(val/1000).toFixed(1)}k`, x + barWidth / 2, y - 8);
  });
}
