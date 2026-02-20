<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="glass-card rounded-2xl shadow-sm p-5 md:p-6">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-2xl font-semibold">Admin Dashboard</h2>
        <span class="text-sm text-slate-500">Manage assignments and workflow</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="text-left border-b border-slate-200 text-slate-600">
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
                        <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($d['pickup_address'], ENT_QUOTES, 'UTF-8') ?></div>
                    </td>
                    <td class="p-3">
                        <?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?>
                        <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($d['donor_email'], ENT_QUOTES, 'UTF-8') ?></div>
                    </td>
                    <td class="p-3">
                        <span class="text-xs px-2 py-1 rounded-full <?= $d['status'] === 'pending' ? 'bg-amber-100 text-amber-700' : ($d['status'] === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') ?>">
                            <?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="p-3">
                        <?php if ($suggested): ?>
                            <div class="font-medium"><?= htmlspecialchars($suggested['name'], ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="text-xs text-slate-500 mt-1"><?= number_format((float) $suggested['distance_km'], 2) ?> km away</div>
                        <?php else: ?>
                            <span class="text-slate-400">No location data</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <?php if ($d['status'] !== 'pending'): ?>
                            <button class="bg-slate-200 text-slate-500 px-3 py-1.5 rounded-lg cursor-not-allowed" disabled>Finalized</button>
                        <?php else: ?>
                            <form method="post" action="<?= htmlspecialchars($basePath . '/donations/assign', ENT_QUOTES, 'UTF-8') ?>" class="space-y-2">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="donation_id" value="<?= (int) $d['id'] ?>">
                                <select name="orphanage_id" class="focus-ring border border-slate-300 rounded-lg p-2 text-sm w-full bg-white">
                                    <option value="">Use nearest suggestion</option>
                                    <?php foreach ($orphanages as $o): ?>
                                        <option value="<?= (int) $o['id'] ?>" <?= ((int) ($suggested['id'] ?? 0) === (int) $o['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($o['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="bg-teal-600 text-white px-3 py-1.5 rounded-lg hover:bg-teal-700">Assign</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
