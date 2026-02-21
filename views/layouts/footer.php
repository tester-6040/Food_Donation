<?php $basePath = $app['base_path'] ?? ''; ?>
  </main>
  <footer class="container-pro px-4 pb-8 text-xs subtle">
    © <?= date('Y') ?> Food Donation Platform · Production-ready NGO workflow suite.
  </footer>
  <script src="<?= htmlspecialchars($basePath . '/assets/js/validation.js', ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
