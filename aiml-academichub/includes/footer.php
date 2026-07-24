    </div><!-- /.main-content -->
  </div><!-- /#app -->

  <!-- Application JS Scripts -->
  <script src="js/data.js"></script>
  <script src="js/processingEngine.js"></script>
  <script src="js/integration.js"></script>
  <script src="js/reportsEngine.js"></script>
  <script src="js/accreditationEngine.js"></script>
  <script src="js/charts.js"></script>
  <script src="js/app.js"></script>

  <?php if (isset($_SESSION['user_role'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (window.App && typeof window.App.setRole === 'function') {
        window.App.currentRole = '<?php echo htmlspecialchars($_SESSION['user_role']); ?>';
        window.App.renderView('dashboard');
      }
    });
  </script>
  <?php endif; ?>

</body>
</html>
