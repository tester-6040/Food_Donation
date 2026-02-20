<?php $basePath = $app['base_path'] ?? ''; ?>
<section class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-5">
        <h2 class="text-xl font-semibold mb-3">Submit Donation</h2>
        <form method="post" action="<?= htmlspecialchars($basePath . '/donations/create', ENT_QUOTES, 'UTF-8') ?>" class="space-y-3" data-validate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
            <input class="w-full border rounded p-2" name="title" required placeholder="Food title">
            <textarea class="w-full border rounded p-2" name="description" required placeholder="Description"></textarea>
            <input class="w-full border rounded p-2" name="quantity" required placeholder="Quantity (e.g. 30 meals)">
            <input class="w-full border rounded p-2" name="pickup_address" required placeholder="Pickup address">
            <div class="grid grid-cols-2 gap-2">
                <input class="w-full border rounded p-2" step="any" type="number" name="pickup_latitude" placeholder="Pickup latitude">
                <input class="w-full border rounded p-2" step="any" type="number" name="pickup_longitude" placeholder="Pickup longitude">
            </div>
            <button class="bg-emerald-600 text-white px-4 py-2 rounded">Submit</button>
        </form>
    </div>

    <div class="bg-white rounded shadow p-5">
        <h2 class="text-xl font-semibold mb-3">My Donations</h2>
        <div class="space-y-2 max-h-[34rem] overflow-auto">
            <?php foreach ($donations as $d): ?>
                <div class="border rounded p-3">
                    <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?> <span class="text-sm">(<?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?>)</span></div>
                    <div class="text-sm text-slate-600">Assigned: <?= htmlspecialchars($d['orphanage_name'] ?? 'Not assigned', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (!$donations): ?><p class="text-slate-600">No donations yet.</p><?php endif; ?>
        </div>
    </div>
</section>
