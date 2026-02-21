<?php $basePath = $app['base_path'] ?? ''; ?>
<div class="max-w-md mx-auto panel p-7 md:p-8 mt-6">
  <h2 class="section-title mb-1">Welcome back</h2>
  <p class="subtle text-sm mb-5">Sign in to manage your donations and assignments.</p>
  <form method="post" action="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>" class="space-y-4">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
    <input class="input-pro" type="email" name="email" required placeholder="Email">
    <input class="input-pro" type="password" name="password" required placeholder="Password">
    <button class="btn btn-brand w-full">Login</button>
  </form>
</div>
