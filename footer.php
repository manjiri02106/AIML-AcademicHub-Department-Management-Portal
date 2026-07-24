<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Footer Component
// ====================================================================
?>
</main> <!-- End app-main-content -->
</div> <!-- End app-body -->

<footer class="app-footer">
    <div class="footer-container">
        <div class="footer-left">
            <p>&copy; <?php echo date('Y'); ?> <strong>AIML Department - AcademicHub</strong>. All rights reserved.</p>
        </div>
        <div class="footer-right">
            <span class="footer-tag">Department Management Portal</span>
            <span class="footer-version">v2.4 Production Ready</span>
        </div>
    </div>
</footer>
</div> <!-- End app-layout -->

<!-- Core Scripts -->
<script src="js/main.js"></script>
<?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
<script src="js/slider.js"></script>
<?php endif; ?>
<?php if (basename($_SERVER['PHP_SELF']) == 'equipment.php'): ?>
<script src="js/equipment.js"></script>
<?php endif; ?>
<?php if (basename($_SERVER['PHP_SELF']) == 'schedule.php'): ?>
<script src="js/schedule.js"></script>
<?php endif; ?>
</body>
</html>
