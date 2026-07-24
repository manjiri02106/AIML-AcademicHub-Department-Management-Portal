<?php
/* AIML ACADEMICHUB - Main Entry Point (index.php) */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

      <!-- Main Dynamic Content Container -->
      <main class="content-container" id="mainAppView">
        <!-- Rendered dynamically by app.js & PHP sessions -->
        <div class="glass-panel">
          <p style="color: var(--cyan);"><i class="lucide-loader-2 spin"></i> Initializing AIML AcademicHub PHP Application Engine...</p>
        </div>
      </main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
