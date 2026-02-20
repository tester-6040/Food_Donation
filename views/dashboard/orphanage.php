<div class="bg-white rounded shadow p-5">
    <h2 class="text-2xl font-semibold mb-4">Orphanage Dashboard</h2>
    <div class="space-y-3">
        <?php foreach ($donations as $d): ?>
            <div class="border rounded p-3">
                <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-sm text-slate-600">Donor: <?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($d['donor_email'], ENT_QUOTES, 'UTF-8') ?>)</div>
                <div class="text-sm">Status: <strong><?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                <?php if ($d['status'] === 'pending'): ?>
                    <form class="mt-2 flex gap-2" method="post" action="/donations/orphanage-action">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="donation_id" value="<?= (int) $d['id'] ?>">
                        <button name="action" value="accept" class="bg-green-600 text-white px-3 py-1 rounded">Accept</button>
                        <button name="action" value="reject" class="bg-red-600 text-white px-3 py-1 rounded">Reject</button>
                    </form>
                <?php else: ?>
                    <button class="mt-2 bg-slate-300 text-slate-600 px-3 py-1 rounded cursor-not-allowed" disabled>Finalized</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (!$donations): ?><p class="text-slate-600">No donations assigned.</p><?php endif; ?>
    </div>
</div>
