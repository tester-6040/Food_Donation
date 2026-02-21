<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="max-w-2xl mx-auto panel p-7 md:p-8 mt-6">
  <h2 class="section-title mb-1">Create your account</h2>
  <p class="subtle text-sm mb-5">Join as donor or orphanage with location info for smart matching.</p>
  <form method="post" action="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>" class="space-y-4" data-validate>
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
    <div class="grid md:grid-cols-2 gap-3">
      <input class="input-pro" name="name" required placeholder="Full name">
      <input class="input-pro" type="email" name="email" required placeholder="Email address">
    </div>
    <input class="input-pro" type="password" minlength="8" name="password" required placeholder="Password (min 8 chars)">
    <select class="input-pro" name="role" required>
      <option value="user">Donor (User)</option>
      <option value="orphanage">Orphanage</option>
    </select>
    <input class="input-pro" name="address" placeholder="Address">
    <div class="grid grid-cols-2 gap-3">
      <input id="register-latitude" class="input-pro" type="number" step="any" name="latitude" placeholder="Latitude">
      <input id="register-longitude" class="input-pro" type="number" step="any" name="longitude" placeholder="Longitude">
    </div>
    <button type="button" data-fill-location data-lat-target="#register-latitude" data-lng-target="#register-longitude" class="btn btn-soft w-full">Use current location</button>
    <button class="btn btn-brand w-full">Create account</button>
  </form>
</div>
