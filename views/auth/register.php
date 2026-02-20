<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="max-w-lg mx-auto bg-white rounded shadow p-6">
    <h2 class="text-2xl font-semibold mb-4">Register</h2>
    <form method="post" action="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>" class="space-y-3" data-validate>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <input class="w-full border rounded p-2" name="name" required placeholder="Name">
        <input class="w-full border rounded p-2" type="email" name="email" required placeholder="Email">
        <input class="w-full border rounded p-2" type="password" minlength="8" name="password" required placeholder="Password (min 8 chars)">
        <select class="w-full border rounded p-2" name="role" required>
            <option value="user">Donor (User)</option>
            <option value="orphanage">Orphanage</option>
        </select>
        <input class="w-full border rounded p-2" name="address" placeholder="Address">
        <div class="grid grid-cols-2 gap-2">
            <input class="w-full border rounded p-2" type="number" step="any" name="latitude" placeholder="Latitude">
            <input class="w-full border rounded p-2" type="number" step="any" name="longitude" placeholder="Longitude">
        </div>
        <button class="w-full bg-emerald-600 text-white py-2 rounded">Create account</button>
    </form>
</div>
