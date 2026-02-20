<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="max-w-md mx-auto glass-card rounded-2xl shadow-sm p-6 md:p-8 mt-6">
    <h2 class="text-2xl font-bold mb-1">Welcome back</h2>
    <p class="text-slate-500 text-sm mb-5">Sign in to continue to your dashboard.</p>
    <form method="post" action="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>" class="space-y-4">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" type="email" name="email" required placeholder="Email">
        <input class="focus-ring w-full border border-slate-300 rounded-lg px-3 py-2.5" type="password" name="password" required placeholder="Password">
        <button class="w-full bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700">Login</button>
    </form>
</div>
