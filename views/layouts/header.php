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
<body class="text-slate-800 min-h-screen">
<nav class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/80 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="<?= htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8') ?>" class="text-lg font-semibold text-teal-700">Food Donation Platform</a>
        <div class="space-x-2 md:space-x-3 text-sm">
            <?php if ($user): ?>
                <a class="px-3 py-2 rounded-lg hover:bg-slate-100" href="<?= htmlspecialchars($basePath . '/dashboard', ENT_QUOTES, 'UTF-8') ?>">Dashboard</a>
                <a class="px-3 py-2 rounded-lg bg-slate-800 text-white hover:bg-slate-700" href="<?= htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8') ?>">Logout</a>
            <?php else: ?>
                <a class="px-3 py-2 rounded-lg hover:bg-slate-100" href="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>">Login</a>
                <a class="px-3 py-2 rounded-lg bg-teal-600 text-white hover:bg-teal-700" href="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto p-4 md:p-6">
    <?php if ($success): ?><div class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error): ?><div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
