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
<body class="bg-slate-100 text-slate-900 min-h-screen">
<nav class="bg-emerald-700 text-white p-4 flex justify-between">
    <a href="<?= htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8') ?>" class="font-semibold">Food Donation Platform</a>
    <div class="space-x-4">
        <?php if ($user): ?>
            <a href="<?= htmlspecialchars($basePath . '/dashboard', ENT_QUOTES, 'UTF-8') ?>">Dashboard</a>
            <a href="<?= htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8') ?>">Logout</a>
        <?php else: ?>
            <a href="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>">Login</a>
            <a href="<?= htmlspecialchars($basePath . '/register', ENT_QUOTES, 'UTF-8') ?>">Register</a>
        <?php endif; ?>
    </div>
</nav>
<main class="max-w-6xl mx-auto p-4">
    <?php if ($success): ?><div class="mb-4 rounded bg-green-100 text-green-800 p-3"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error): ?><div class="mb-4 rounded bg-red-100 text-red-800 p-3"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
