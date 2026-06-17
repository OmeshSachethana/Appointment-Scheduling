<?php
$pdo = getDBConnection();
$unreadCount = isLoggedIn() ? getUnreadNotificationCount($pdo, $_SESSION['user_id']) : 0;
?>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= basePath('index.php') ?>">
            <span class="brand-logo-wrap" aria-hidden="true">
                <img src="<?= logoPath() ?>" alt="<?= e(__('app_short')) ?>" class="brand-logo">
            </span>
            <span class="brand-text">
                <span class="brand-title"><?= e(__('app_short')) ?></span>
                <span class="brand-subtitle d-none d-md-inline"><?= e(__('citizen_portal')) ?></span>
            </span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/dashboard.php') ?>"><i class="bi bi-speedometer2 me-1"></i><?= e(__('dashboard')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/users.php') ?>"><i class="bi bi-people me-1"></i><?= e(__('users')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/appointments.php') ?>"><i class="bi bi-calendar-check me-1"></i><?= e(__('appointments')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/service-requests.php') ?>"><i class="bi bi-file-earmark-text me-1"></i><?= e(__('service_requests')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/reports.php') ?>"><i class="bi bi-bar-chart me-1"></i><?= e(__('reports')) ?></a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('citizen/dashboard.php') ?>"><i class="bi bi-speedometer2 me-1"></i><?= e(__('dashboard')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('citizen/appointments/index.php') ?>"><i class="bi bi-calendar-check me-1"></i><?= e(__('appointments')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('citizen/service-requests/index.php') ?>"><i class="bi bi-file-earmark-text me-1"></i><?= e(__('service_requests')) ?></a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= basePath(isAdmin() ? 'admin/notifications.php' : 'citizen/notifications.php') ?>">
                            <i class="bi bi-bell me-1"></i><?= e(__('notifications')) ?>
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-translate"></i> <?= e(__('language')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item <?= $currentLang === 'en' ? 'active' : '' ?>" href="<?= langUrl('en') ?>"><?= e(__('english')) ?></a></li>
                        <li><a class="dropdown-item <?= $currentLang === 'si' ? 'active' : '' ?>" href="<?= langUrl('si') ?>"><?= e(__('sinhala')) ?></a></li>
                    </ul>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?= e($_SESSION['full_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isCitizen()): ?>
                                <li><a class="dropdown-item" href="<?= basePath('citizen/profile.php') ?>"><i class="bi bi-person me-2"></i><?= e(__('profile')) ?></a></li>
                                <li><a class="dropdown-item" href="<?= basePath('citizen/change-password.php') ?>"><i class="bi bi-key me-2"></i><?= e(__('change_password')) ?></a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= basePath('auth/logout.php') ?>"><i class="bi bi-box-arrow-right me-2"></i><?= e(__('logout')) ?></a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= basePath('auth/login.php') ?>"><?= e(__('login')) ?></a></li>
                    <li class="nav-item"><a class="nav-link btn-nav-cta px-3" href="<?= basePath('auth/register.php') ?>"><?= e(__('register')) ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
