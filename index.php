<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = __('home');
include __DIR__ . '/includes/header.php';
?>

<section class="hero-section text-white text-center py-5 mb-5">
    <div class="container py-5">
        <div class="hero-logo-wrap mx-auto mb-4" aria-hidden="true">
            <img src="<?= logoPath() ?>" alt="<?= e(__('app_short')) ?>" class="hero-logo">
        </div>
        <div class="hero-badge mx-auto">
            <?= e(__('app_short')) ?>
        </div>
        <h1 class="display-5 fw-bold mb-3"><?= e(__('app_name')) ?></h1>
        <p class="lead col-lg-8 mx-auto mb-0"><?= e(__('welcome_message')) ?></p>
        <div class="d-flex gap-3 justify-content-center flex-wrap mt-5 hero-actions">
            <a href="<?= basePath('auth/register.php') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-person-plus me-1"></i> <?= e(__('register')) ?>
            </a>
            <a href="<?= basePath('auth/login.php') ?>" class="btn btn-outline-light btn-lg">
                <i class="bi bi-box-arrow-in-right me-1"></i> <?= e(__('citizen_login')) ?>
            </a>
            <a href="<?= basePath('auth/admin-login.php') ?>" class="btn btn-outline-warning btn-lg">
                <i class="bi bi-shield-lock me-1"></i> <?= e(__('admin_login')) ?>
            </a>
        </div>
    </div>
</section>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card feature-card h-100 border-0">
            <div class="card-body text-center p-4 p-lg-5">
                <div class="feature-icon feature-icon--primary mx-auto mb-3">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h5><?= e(__('appointments')) ?></h5>
                <p class="text-muted mb-0"><?= e(__('create_appointment')) ?> &middot; <?= e(__('view_appointments')) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card feature-card h-100 border-0">
            <div class="card-body text-center p-4 p-lg-5">
                <div class="feature-icon feature-icon--success mx-auto mb-3">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h5><?= e(__('service_requests')) ?></h5>
                <p class="text-muted mb-0"><?= e(__('submit_request')) ?> &middot; <?= e(__('track_requests')) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card feature-card h-100 border-0">
            <div class="card-body text-center p-4 p-lg-5">
                <div class="feature-icon feature-icon--info mx-auto mb-3">
                    <i class="bi bi-bell"></i>
                </div>
                <h5><?= e(__('notifications')) ?></h5>
                <p class="text-muted mb-0"><?= e(__('notifications')) ?></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
