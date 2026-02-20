<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="max-w-md mx-auto bg-white rounded shadow p-6">
    <h2 class="text-2xl font-semibold mb-4">Login</h2>
    <form method="post" action="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>" class="space-y-3">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <input class="w-full border rounded p-2" type="email" name="email" required placeholder="Email">
        <input class="w-full border rounded p-2" type="password" name="password" required placeholder="Password">
        <button class="w-full bg-emerald-600 text-white py-2 rounded">Login</button>
    </form>
</div>
