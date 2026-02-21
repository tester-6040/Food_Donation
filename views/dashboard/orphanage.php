<?php
$basePath = $app['base_path'] ?? '';
$badgeClass = static fn(string $s): string => $s === 'pending' ? 'badge-pending' : ($s === 'accepted' ? 'badge-accepted' : 'badge-rejected');
?>
<div class="panel p-5 md:p-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="section-title">Orphanage Dashboard</h2>
    <span class="subtle text-sm"><?= count($donations) ?> assigned</span>
  </div>
  <div class="space-y-4">
    <?php foreach ($donations as $d): ?>
      <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="flex items-center justify-between gap-3">
          <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></div>
          <span class="badge <?= $badgeClass($d['status']) ?>"><?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="text-sm subtle mt-2">Donor: <?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($d['donor_email'], ENT_QUOTES, 'UTF-8') ?>)</div>
        <?php if ($d['status'] === 'pending'): ?>
          <form class="mt-3 flex gap-2" method="post" action="<?= htmlspecialchars($basePath . '/donations/orphanage-action', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="donation_id" value="<?= (int) $d['id'] ?>">
            <button name="action" value="accept" class="btn" style="background:#16a34a;color:white;">Accept</button>
            <button name="action" value="reject" class="btn" style="background:#dc2626;color:white;">Reject</button>
          </form>
        <?php else: ?>
          <button class="btn btn-soft mt-3 cursor-not-allowed opacity-70" disabled>Finalized</button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (!$donations): ?><p class="subtle">No donations assigned.</p><?php endif; ?>
  </div>
</div>
