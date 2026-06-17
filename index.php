<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = __('home');
include __DIR__ . '/includes/header.php';
?>

<section class="hero-section text-white text-center py-5 mb-4 rounded-3">
    <div class="container py-5">
        <h1 class="display-5 fw-bold"><?= e(__('app_name')) ?></h1>
        <p class="lead col-lg-8 mx-auto"><?= e(__('welcome_message')) ?></p>
        <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
            <a href="<?= basePath('auth/register.php') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-person-plus"></i> <?= e(__('register')) ?>
            </a>
            <a href="<?= basePath('auth/login.php') ?>" class="btn btn-outline-light btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> <?= e(__('citizen_login')) ?>
            </a>
            <a href="<?= basePath('auth/admin-login.php') ?>" class="btn btn-outline-warning btn-lg">
                <i class="bi bi-shield-lock"></i> <?= e(__('admin_login')) ?>
            </a>
        </div>
    </div>
</section>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-3">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
                <h5><?= e(__('appointments')) ?></h5>
                <p class="text-muted"><?= e(__('create_appointment')) ?> <?= e(__('view_appointments')) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-success text-white rounded-circle mx-auto mb-3">
                    <i class="bi bi-file-earmark-text fs-3"></i>
                </div>
                <h5><?= e(__('service_requests')) ?></h5>
                <p class="text-muted"><?= e(__('submit_request')) ?> <?= e(__('track_requests')) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-info text-white rounded-circle mx-auto mb-3">
                    <i class="bi bi-bell fs-3"></i>
                </div>
                <h5><?= e(__('notifications')) ?></h5>
                <p class="text-muted"><?= e(__('notifications')) ?></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
