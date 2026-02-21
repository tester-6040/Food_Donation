<?php
$basePath = $app['base_path'] ?? '';
$badgeClass = static fn(string $s): string => $s === 'pending' ? 'badge-pending' : ($s === 'accepted' ? 'badge-accepted' : 'badge-rejected');
?>
<div class="panel p-5 md:p-6">
  <div class="flex flex-wrap gap-3 items-end justify-between mb-5">
    <div>
      <h2 class="section-title">Admin Control Center</h2>
      <p class="subtle text-sm">Review donations, verify status, and assign the best orphanage.</p>
    </div>
    <div class="subtle text-sm">Total records: <?= count($donations) ?></div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left border-b border-slate-200 subtle">
          <th class="p-3">Donation</th>
          <th class="p-3">Donor</th>
          <th class="p-3">Status</th>
          <th class="p-3">Nearest Suggestion</th>
          <th class="p-3">Assign</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($donations as $d): ?>
        <?php $suggested = $suggestions[$d['id']] ?? null; ?>
        <tr class="border-b border-slate-100 align-top">
          <td class="p-3">
            <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="text-xs subtle mt-1"><?= htmlspecialchars($d['pickup_address'], ENT_QUOTES, 'UTF-8') ?></div>
          </td>
          <td class="p-3">
            <?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?>
            <div class="text-xs subtle mt-1"><?= htmlspecialchars($d['donor_email'], ENT_QUOTES, 'UTF-8') ?></div>
          </td>
          <td class="p-3"><span class="badge <?= $badgeClass($d['status']) ?>"><?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
          <td class="p-3">
            <?php if ($suggested): ?>
              <div class="font-medium"><?= htmlspecialchars($suggested['name'], ENT_QUOTES, 'UTF-8') ?></div>
              <div class="text-xs subtle mt-1"><?= number_format((float) $suggested['distance_km'], 2) ?> km away</div>
            <?php else: ?>
              <span class="subtle">No location data</span>
            <?php endif; ?>
          </td>
          <td class="p-3">
            <?php if ($d['status'] !== 'pending'): ?>
              <button class="btn btn-soft cursor-not-allowed opacity-70" disabled>Finalized</button>
            <?php else: ?>
              <form method="post" action="<?= htmlspecialchars($basePath . '/donations/assign', ENT_QUOTES, 'UTF-8') ?>" class="space-y-2">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="donation_id" value="<?= (int) $d['id'] ?>">
                <select name="orphanage_id" class="input-pro text-sm">
                  <option value="">Use nearest suggestion</option>
                  <?php foreach ($orphanages as $o): ?>
                    <option value="<?= (int) $o['id'] ?>" <?= ((int) ($suggested['id'] ?? 0) === (int) $o['id']) ? 'selected' : '' ?>><?= htmlspecialchars($o['name'], ENT_QUOTES, 'UTF-8') ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-brand">Assign</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
