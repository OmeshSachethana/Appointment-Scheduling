    </div>
</main>
<footer class="app-footer">
    <div class="container">
        <div class="row align-items-start g-4">
            <div class="col-md-8">
                <h6><i class="bi bi-building footer-icon"></i><?= e(__('app_short')) ?></h6>
                <p class="mb-0 small"><?= e(__('footer_text')) ?></p>
            </div>
            <div class="col-md-4 text-md-end">
                <p class="mb-0 small"><i class="bi bi-geo-alt footer-icon"></i><?= e(__('office_address')) ?></p>
            </div>
        </div>
        <hr class="footer-divider">
        <p class="footer-meta mb-0 text-center">&copy; <?= date('Y') ?> <?= e(__('app_short')) ?> &middot; <?= e(__('citizen_portal')) ?> &amp; <?= e(__('admin_portal')) ?></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= basePath('assets/js/main.js') ?>"></script>
</body>
</html>
