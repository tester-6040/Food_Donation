<?php $basePath = $app['base_path'] ?? ''; ?>
<section class="grid lg:grid-cols-2 gap-6 items-stretch">
    <div class="glass-card rounded-2xl p-8 shadow-sm">
        <span class="inline-flex items-center rounded-full bg-teal-100 text-teal-700 px-3 py-1 text-xs font-medium mb-4">NGO Food Rescue Network</span>
        <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-4">Donate extra food. Deliver hope to the nearest orphanage.</h1>
        <p class="text-slate-600 mb-6">A streamlined platform for donors, admins, and orphanages with assignment automation, real-time status handling, and secure role-based access.</p>
        <div class="flex flex-wrap gap-3">
            <a href="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>" class="inline-block bg-teal-600 text-white px-5 py-2.5 rounded-lg hover:bg-teal-700">Create Account</a>
            <a href="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>" class="inline-block bg-white border border-slate-300 px-5 py-2.5 rounded-lg hover:bg-slate-50">Sign In</a>
        </div>
    </div>

    <div class="rounded-2xl bg-slate-900 text-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold mb-4">How it works</h2>
        <ul class="space-y-3 text-slate-200 text-sm">
            <li>1. Donor submits donation details + pickup location.</li>
            <li>2. Admin reviews and assigns nearest orphanage.</li>
            <li>3. Orphanage accepts/rejects with one click.</li>
            <li>4. Email notifications keep everyone informed.</li>
        </ul>
    </div>
</section>
