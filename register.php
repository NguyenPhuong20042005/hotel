<?php include __DIR__ . '/header.php'; ?>

<main class="container">
  <div style="max-width: 480px; margin: 2rem auto;">
    <div class="card" style="padding: 2.5rem;">
      <div style="text-align: center; margin-bottom: 2rem;">
        <i class="fa-solid fa-user-plus" style="font-size: 2.5rem; color: var(--accent-gold); margin-bottom: 1rem;"></i>
        <h2 style="font-size: 1.6rem; font-weight: 700;">Create Guest Account</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Register to manage your stays and unlock exclusive perks</p>
      </div>

      <form action="/register/submit" method="POST">
        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label>Full Name</label>
          <input type="text" name="full_name" class="form-control" placeholder="e.g. Tran Minh Duc" required>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label>Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="e.g. minhduc@example.com" required>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
          <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control" placeholder="+84 912 345...">
          </div>
          <div class="form-group">
            <label>City</label>
            <input type="text" name="city" class="form-control" placeholder="Ha Noi">
          </div>
        </div>

        <button type="submit" class="btn-accent" style="width: 100%; height: 48px; justify-content: center; font-size: 1rem;">
          Register Account
        </button>
      </form>

      <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
        Already have an account? <a href="/login" style="color: var(--accent-gold); font-weight: 600;">Sign In Here</a>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
