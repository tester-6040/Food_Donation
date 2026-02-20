<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="glass-card rounded-2xl shadow-sm p-5 md:p-6">
    <h2 class="text-2xl font-semibold mb-4">Orphanage Dashboard</h2>
    <div class="space-y-4">
        <?php foreach ($donations as $d): ?>
            <div class="border border-slate-200 rounded-xl p-4 bg-white">
                <div class="flex items-center justify-between">
                    <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></div>
                    <span class="text-xs px-2 py-1 rounded-full <?= $d['status'] === 'pending' ? 'bg-amber-100 text-amber-700' : ($d['status'] === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') ?>">
                        <?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
                <div class="text-sm text-slate-600 mt-2">Donor: <?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($d['donor_email'], ENT_QUOTES, 'UTF-8') ?>)</div>
                <?php if ($d['status'] === 'pending'): ?>
                    <form class="mt-3 flex gap-2" method="post" action="<?= htmlspecialchars($basePath . '/donations/orphanage-action', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="donation_id" value="<?= (int) $d['id'] ?>">
                        <button name="action" value="accept" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Accept</button>
                        <button name="action" value="reject" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Reject</button>
                    </form>
                <?php else: ?>
                    <button class="mt-3 bg-slate-200 text-slate-500 px-4 py-2 rounded-lg cursor-not-allowed" disabled>Finalized</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (!$donations): ?><p class="text-slate-500">No donations assigned.</p><?php endif; ?>
    </div>
</div>
