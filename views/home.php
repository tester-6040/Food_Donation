<?php $basePath = $app['base_path'] ?? ''; ?>
<section class="bg-white rounded shadow p-8">
    <h1 class="text-3xl font-bold mb-4">Help Share Surplus Food</h1>
    <p class="mb-4">Donors can submit food donations, admins assign nearby orphanages, and orphanages can accept or reject requests quickly.</p>
    <a href="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>" class="inline-block bg-emerald-600 text-white px-4 py-2 rounded">Get Started</a>
</section>
