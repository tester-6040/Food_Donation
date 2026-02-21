<?php
$basePath = $app['base_path'] ?? '';
$badgeClass = static fn(string $s): string => $s === 'pending' ? 'badge-pending' : ($s === 'accepted' ? 'badge-accepted' : 'badge-rejected');
?>
<section class="grid xl:grid-cols-5 gap-6">
  <div class="xl:col-span-2 panel p-5 md:p-6">
    <h2 class="section-title mb-1">Submit Donation</h2>
    <p class="subtle text-sm mb-4">Add precise pickup details to improve nearest orphanage assignment.</p>
    <form method="post" action="<?= htmlspecialchars($basePath . '/donations/create', ENT_QUOTES, 'UTF-8') ?>" class="space-y-3" data-validate>
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <input class="input-pro" name="title" required placeholder="Food title">
      <textarea class="input-pro" name="description" required placeholder="Description"></textarea>
      <input class="input-pro" name="quantity" required placeholder="Quantity (e.g. 30 meals)">
      <input class="input-pro" name="pickup_address" required placeholder="Pickup address">
      <div class="grid grid-cols-2 gap-3">
        <input id="donation-latitude" class="input-pro" step="any" type="number" name="pickup_latitude" placeholder="Latitude">
        <input id="donation-longitude" class="input-pro" step="any" type="number" name="pickup_longitude" placeholder="Longitude">
      </div>
      <button type="button" data-fill-location data-lat-target="#donation-latitude" data-lng-target="#donation-longitude" class="btn btn-soft w-full">Use current pickup location</button>
      <button class="btn btn-brand w-full">Submit Donation</button>
    </form>
  </div>

  <div class="xl:col-span-3 panel p-5 md:p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="section-title">My Donations</h2>
      <span class="subtle text-sm"><?= count($donations) ?> total</span>
    </div>
    <div class="space-y-3 max-h-[34rem] overflow-auto pr-1">
      <?php foreach ($donations as $d): ?>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
          <div class="flex items-center justify-between gap-3">
            <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></div>
            <span class="badge <?= $badgeClass($d['status']) ?>"><?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="text-sm subtle mt-2">Assigned: <?= htmlspecialchars($d['orphanage_name'] ?? 'Not assigned', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endforeach; ?>
      <?php if (!$donations): ?><p class="subtle">No donations yet.</p><?php endif; ?>
    </div>
  </div>
</section>
