<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="max-w-xl mx-auto glass-card rounded-2xl shadow-sm p-6 md:p-8 mt-6">
    <h2 class="text-2xl font-bold mb-1">Create account</h2>
    <p class="text-slate-500 text-sm mb-5">Join as donor or orphanage partner.</p>
    <form method="post" action="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>" class="space-y-4" data-validate>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" name="name" required placeholder="Full name">
        <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" type="email" name="email" required placeholder="Email address">
        <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" type="password" minlength="8" name="password" required placeholder="Password (min 8 chars)">
        <select class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" name="role" required>
            <option value="user">Donor (User)</option>
            <option value="orphanage">Orphanage</option>
        </select>
        <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" name="address" placeholder="Address">
        <div class="grid grid-cols-2 gap-3">
            <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" type="number" step="any" name="latitude" placeholder="Latitude">
            <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" type="number" step="any" name="longitude" placeholder="Longitude">
        </div>
        <button class="w-full bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700">Create account</button>
    </form>
</div>
