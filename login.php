<?php include __DIR__ . '/header.php'; ?>

<main class="container">
  <div style="max-width: 440px; margin: 2rem auto;">
    <div class="card" style="padding: 2.5rem;">
      <div style="text-align: center; margin-bottom: 2rem;">
        <i class="fa-solid fa-lock" style="font-size: 2.5rem; color: var(--accent-gold); margin-bottom: 1rem;"></i>
        <h2 style="font-size: 1.6rem; font-weight: 700;">Account Sign In</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Access your booking portal & customer profile</p>
      </div>

      <form action="/login/submit" method="POST">
        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label>Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="e.g. vananh@gmail.com" required>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-accent" style="width: 100%; height: 48px; justify-content: center; font-size: 1rem;">
          Sign In
        </button>
      </form>

      <div style="margin-top: 1.5rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
        Demo Customer: <strong>vananh@gmail.com</strong> / password123 <br>
        Demo Admin: <strong>admin@grandvista.com</strong> / password123
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
