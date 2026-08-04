<?php include __DIR__ . '/header.php'; ?>

<main class="container">
  <div class="section-header">
    <div>
      <span style="color: var(--accent-gold); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Executive Dashboard</span>
      <h2 class="section-title">Hotel Chain Business Analytics</h2>
    </div>
    <div>
      <button onclick="window.print()" class="btn-outline"><i class="fa-solid fa-print"></i> Export Analytics Report</button>
    </div>
  </div>

  <!-- Metric KPI Cards -->
  <div class="metrics-grid">
    <div class="stat-card">
      <div class="stat-icon"><i class="fa-solid fa-bed"></i></div>
      <div>
        <div class="stat-val"><?= e($occupancy['occupancy_rate_pct']) ?>%</div>
        <div class="stat-lbl">Occupancy Rate (Live)</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="color: var(--accent-emerald); background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3);">
        <i class="fa-solid fa-sack-dollar"></i>
      </div>
      <div>
        <div class="stat-val"><?= formatCurrency($revenue['total_revenue']) ?></div>
        <div class="stat-lbl">Total Gross Revenue</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="color: var(--accent-purple); background: rgba(139, 92, 246, 0.1); border-color: rgba(139, 92, 246, 0.3);">
        <i class="fa-solid fa-users"></i>
      </div>
      <div>
        <div class="stat-val"><?= count($demographics) ?></div>
        <div class="stat-lbl">Demographic Regions</div>
      </div>
    </div>
  </div>

  <!-- Analytics Layout Grid -->
  <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 2rem; margin-bottom: 3rem;">
    
    <!-- Revenue Trends Bar Chart -->
    <div class="card" style="padding: 1.75rem;">
      <h3 style="font-size: 1.2rem; margin-bottom: 0.25rem;">Revenue Trends (Monthly)</h3>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">Historical monthly revenue performance across all branches</p>

      <div style="display: flex; justify-content: center; align-items: center;">
        <canvas id="revenueChartCanvas" width="500" height="250"></canvas>
      </div>
    </div>

    <!-- Customer Demographics Table -->
    <div class="card" style="padding: 1.75rem;">
      <h3 style="font-size: 1.2rem; margin-bottom: 0.25rem;">Customer Demographics</h3>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">Guest distribution by country of origin</p>

      <div class="table-card" style="border: none;">
        <table class="custom-table">
          <thead>
            <tr>
              <th>Country</th>
              <th>Guests</th>
              <th>Share</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $totalGuests = array_sum(array_column($demographics, 'count')) ?: 1;
            foreach ($demographics as $demo): 
              $pct = round(($demo['count'] / $totalGuests) * 100);
            ?>
              <tr>
                <td><strong><?= e($demo['country']) ?></strong></td>
                <td><?= e($demo['count']) ?></td>
                <td>
                  <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                      <div style="width: <?= $pct ?>%; height: 100%; background: var(--accent-gold);"></div>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--text-muted);"><?= $pct ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
