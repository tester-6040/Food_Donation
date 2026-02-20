<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="bg-white rounded shadow p-5">
    <h2 class="text-2xl font-semibold mb-4">Admin Dashboard</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="text-left border-b">
                <th class="p-2">Donation</th>
                <th class="p-2">Donor</th>
                <th class="p-2">Status</th>
                <th class="p-2">Suggested Nearest Orphanage</th>
                <th class="p-2">Assign</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($donations as $d): ?>
                <?php $suggested = $suggestions[$d['id']] ?? null; ?>
                <tr class="border-b align-top">
                    <td class="p-2">
                        <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="text-xs text-slate-600"><?= htmlspecialchars($d['pickup_address'], ENT_QUOTES, 'UTF-8') ?></div>
                    </td>
                    <td class="p-2"><?= htmlspecialchars($d['donor_name'], ENT_QUOTES, 'UTF-8') ?><br><span class="text-xs"><?= htmlspecialchars($d['donor_email'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td class="p-2"><span class="px-2 py-1 rounded bg-slate-100"><?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td class="p-2">
                        <?php if ($suggested): ?>
                            <?= htmlspecialchars($suggested['name'], ENT_QUOTES, 'UTF-8') ?>
                            <div class="text-xs text-slate-600"><?= number_format((float) $suggested['distance_km'], 2) ?> km away</div>
                        <?php else: ?>
                            <span class="text-slate-500">No location data</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-2">
                        <?php if ($d['status'] !== 'pending'): ?>
                            <button class="bg-slate-300 text-slate-600 px-3 py-1 rounded cursor-not-allowed" disabled>Finalized</button>
                        <?php else: ?>
                            <form method="post" action="<?= htmlspecialchars($basePath . '/donations/assign', ENT_QUOTES, 'UTF-8') ?>" class="space-y-2">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="donation_id" value="<?= (int) $d['id'] ?>">
                                <select name="orphanage_id" class="border rounded p-1 text-sm w-full">
                                    <option value="">Use nearest suggestion</option>
                                    <?php foreach ($orphanages as $o): ?>
                                        <option value="<?= (int) $o['id'] ?>" <?= ((int) ($suggested['id'] ?? 0) === (int) $o['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($o['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="bg-indigo-600 text-white px-3 py-1 rounded">Assign</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
