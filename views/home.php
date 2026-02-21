<?php $basePath = $app['base_path'] ?? ''; ?>
<section class="hero p-8 md:p-12 panel border-0">
  <div class="grid lg:grid-cols-2 gap-8 items-center">
    <div>
      <p class="uppercase tracking-widest text-xs text-cyan-200 mb-3">Smart NGO Operations</p>
      <h1 class="text-4xl md:text-5xl font-black leading-tight mb-4">Rescue food faster. Route it to the nearest orphanage.</h1>
      <p class="text-slate-200 mb-6">A secure donation workflow for donors, admins, and orphanages with automated distance-based assignment and real-time status updates.</p>
      <div class="flex flex-wrap gap-3">
        <a href="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-brand">Create account</a>
        <a href="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-soft">Sign in</a>
      </div>
    </div>
    <div class="panel p-6 bg-white/10 border-white/20 text-slate-100">
      <h3 class="font-bold mb-3">End-to-end workflow</h3>
      <ol class="list-decimal pl-5 space-y-2 text-sm">
        <li>Donor submits food details and pickup coordinates.</li>
        <li>Admin reviews and assigns the nearest orphanage.</li>
        <li>Orphanage accepts/rejects from dashboard.</li>
        <li>All parties receive role-based email notifications.</li>
      </ol>
    </div>
  </div>
</section>
