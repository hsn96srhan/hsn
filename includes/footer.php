<?php
$settings = getSiteSettings();
?>
<footer class="site-footer">
    <div class="container text-center">
        <p class="footer-copy" data-en="All Rights Reserved © HASSAN SARHAN"><?php echo t('rights_reserved'); ?></p>
    </div>
</footer>

<script src="assets/js/main.js"></script>

<!-- Script Body Injection -->
<?php if (!empty($settings['script_body'])): ?>
    <?php echo $settings['script_body']; ?>
<?php endif; ?>

</body>
</html>
