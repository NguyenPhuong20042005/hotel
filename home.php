<?php include __DIR__ . '/header.php'; ?>

<section class="hero-section">
  <h1 class="hero-title">Experience Luxury Across <span>Vietnam</span></h1>
  <p class="hero-subtitle">Book your stays online with real-time room availability, instant payment confirmation, and premium hospitalities.</p>

  <div class="search-card">
    <form action="/rooms" method="GET" class="search-form">
      <div class="form-group">
        <label><i class="fa-solid fa-map-pin"></i> Destination</label>
        <select name="city" class="form-control">
          <option value="">All Locations</option>
          <option value="Ha Noi">Ha Noi</option>
          <option value="Da Nang">Da Nang</option>
          <option value="Ho Chi Minh City">Ho Chi Minh City</option>
          <option value="Nha Trang">Nha Trang</option>
          <option value="Phu Quoc">Phu Quoc</option>
          <option value="Da Lat">Da Lat</option>
          <option value="Hue">Hue</option>
          <option value="Quy Nhon">Quy Nhon</option>
          <option value="Cat Ba">Cat Ba</option>
        </select>
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-calendar-days"></i> Check In</label>
        <input type="date" name="check_in" class="form-control" value="<?= date('Y-m-d') ?>">
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-calendar-check"></i> Check Out</label>
        <input type="date" name="check_out" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
      </div>

      <div class="form-group">
        <label><i class="fa-solid fa-user-group"></i> Guests</label>
        <select name="guests" class="form-control">
          <option value="1">1 Guest</option>
          <option value="2" selected>2 Guests</option>
          <option value="3">3 Guests</option>
          <option value="4">4+ Guests</option>
        </select>
      </div>

      <button type="submit" class="btn-accent" style="height: 48px; justify-content: center;">
        <i class="fa-solid fa-magnifying-glass"></i> Search
      </button>
    </form>
  </div>
</section>

<main class="container">
  <?php $hotels = $hotels ?? []; ?>

  <div class="section-header">
    <div>
      <span style="color: var(--accent-gold); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Featured Properties</span>
      <h2 class="section-title">Our Hotel Chain Branches</h2>
    </div>
    <a href="/rooms" class="btn-outline">View All Rooms <i class="fa-solid fa-arrow-right"></i></a>
  </div>

  <?php if (empty($hotels)): ?>
    <div style="text-align: center; padding: 3rem 2rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
      <h3>No hotels available right now</h3>
      <p style="color: var(--text-muted); margin-top: 0.5rem;">Please check the database or refresh the hotel seed data.</p>
    </div>
  <?php else: ?>
  <div class="grid-3">
    <?php foreach ($hotels as $hotel): ?>
      <div class="card">
        <div class="card-img-wrap">
          <img src="<?= e($hotel['image_url']) ?>" alt="<?= e($hotel['name']) ?>" class="card-img">
          <div class="badge-tag"><i class="fa-solid fa-star"></i> <?= e($hotel['star_rating']) ?></div>
        </div>
        <div class="card-body">
          <h3 class="card-title"><?= e($hotel['name']) ?></h3>
          <p class="card-sub"><i class="fa-solid fa-location-dot" style="color: var(--accent-gold);"></i> <?= e($hotel['address']) ?></p>
          <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.5rem; flex: 1;">
            <?= e(substr($hotel['description'], 0, 110)) ?>...
          </p>
          <a href="/rooms?city=<?= urlencode($hotel['city']) ?>" class="btn-outline" style="text-align: center;">Explore Rooms in <?= e($hotel['city']) ?></a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/footer.php'; ?>
