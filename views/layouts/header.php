<?php
Session::start();
$user = Session::get('user');
$success = Session::flash('success');
$error = Session::flash('error');
$basePath = $app['base_path'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($app['app_name'], ENT_QUOTES, 'UTF-8') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/app.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
  <header class="sticky top-0 z-30 bg-white/85 backdrop-blur border-b border-slate-200">
    <div class="container-pro px-4 py-3 flex items-center justify-between">
      <a href="<?= htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8') ?>" class="text-slate-900 font-extrabold tracking-tight">Food Donation Platform</a>
      <nav class="flex items-center gap-2 text-sm">
        <?php if ($user): ?>
          <a class="btn btn-soft" href="<?= htmlspecialchars($basePath . '/dashboard', ENT_QUOTES, 'UTF-8') ?>">Dashboard</a>
          <a class="btn btn-dark" href="<?= htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8') ?>">Logout</a>
        <?php else: ?>
          <a class="btn btn-soft" href="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>">Login</a>
          <a class="btn btn-brand" href="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>">Get Started</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="container-pro px-4 py-6 md:py-8">
    <?php if ($success): ?><div class="mb-4 panel px-4 py-3 border-green-200 text-green-800"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error): ?><div class="mb-4 panel px-4 py-3 border-red-200 text-red-700"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
