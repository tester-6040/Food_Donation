<?php $basePath = $app['base_path'] ?? ''; ?>
<section class="grid xl:grid-cols-5 gap-6">
    <div class="xl:col-span-2 glass-card rounded-2xl shadow-sm p-5 md:p-6">
        <h2 class="text-xl font-semibold mb-1">Submit Donation</h2>
        <p class="text-sm text-slate-500 mb-4">Provide accurate pickup details for faster assignment.</p>
        <form method="post" action="<?= htmlspecialchars($basePath . '/donations/create', ENT_QUOTES, 'UTF-8') ?>" class="space-y-3" data-validate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
            <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" name="title" required placeholder="Food title">
            <textarea class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" name="description" required placeholder="Description"></textarea>
            <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" name="quantity" required placeholder="Quantity (e.g. 30 meals)">
            <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" name="pickup_address" required placeholder="Pickup address">
            <div class="grid grid-cols-2 gap-3">
                <input id="donation-latitude" class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" step="any" type="number" name="pickup_latitude" placeholder="Latitude">
                <input id="donation-longitude" class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" step="any" type="number" name="pickup_longitude" placeholder="Longitude">
            </div>
            <button type="button" data-fill-location data-lat-target="#donation-latitude" data-lng-target="#donation-longitude" class="w-full border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg hover:bg-slate-50">Use current pickup location</button>
            <button class="w-full bg-teal-600 text-white px-4 py-2.5 rounded-lg hover:bg-teal-700">Submit Donation</button>
        </form>
    </div>

    <div class="xl:col-span-3 glass-card rounded-2xl shadow-sm p-5 md:p-6">
        <h2 class="text-xl font-semibold mb-4">My Donations</h2>
        <div class="space-y-3 max-h-[34rem] overflow-auto pr-1">
            <?php foreach ($donations as $d): ?>
                <div class="border border-slate-200 rounded-xl p-4 bg-white">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-semibold"><?= htmlspecialchars($d['title'], ENT_QUOTES, 'UTF-8') ?></div>
                        <span class="text-xs px-2 py-1 rounded-full <?= $d['status'] === 'pending' ? 'bg-amber-100 text-amber-700' : ($d['status'] === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') ?>">
                            <?= htmlspecialchars($d['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <div class="text-sm text-slate-600 mt-2">Assigned: <?= htmlspecialchars($d['orphanage_name'] ?? 'Not assigned', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (!$donations): ?><p class="text-slate-500">No donations yet.</p><?php endif; ?>
        </div>
    </div>
</section>
