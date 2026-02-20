<?php

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        extract($data);
        $app = require __DIR__ . '/../config/app.php';
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $template . '.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    protected function redirect(string $path): void
    {
        $app = require __DIR__ . '/../config/app.php';
        $basePath = $app['base_path'] ?: '';
        $target = $basePath . ($path === '/' ? '/' : '/' . ltrim($path, '/'));
        header('Location: ' . $target);
        exit;
    }

    protected function requireAuth(?string $role = null): array
    {
        Session::start();
        $user = Session::get('user');
        if (!$user) {
            Session::flash('error', 'Please login first.');
            $this->redirect('/login');
        }

        if ($role !== null && $user['role'] !== $role) {
            http_response_code(403);
            exit('Forbidden');
        }

        return $user;
    }

    protected function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
