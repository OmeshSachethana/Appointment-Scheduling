<?php
$pdo = getDBConnection();
$unreadCount = isLoggedIn() ? getUnreadNotificationCount($pdo, $_SESSION['user_id']) : 0;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= basePath('index.php') ?>">
            <i class="bi bi-building"></i> <?= e(__('app_short')) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/dashboard.php') ?>"><?= e(__('dashboard')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/users.php') ?>"><?= e(__('users')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/appointments.php') ?>"><?= e(__('appointments')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/service-requests.php') ?>"><?= e(__('service_requests')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('admin/reports.php') ?>"><?= e(__('reports')) ?></a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('citizen/dashboard.php') ?>"><?= e(__('dashboard')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('citizen/appointments/index.php') ?>"><?= e(__('appointments')) ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= basePath('citizen/service-requests/index.php') ?>"><?= e(__('service_requests')) ?></a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= basePath(isAdmin() ? 'admin/notifications.php' : 'citizen/notifications.php') ?>">
                            <?= e(__('notifications')) ?>
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-danger"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-lg-center">
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
                            <i class="bi bi-person-circle"></i> <?= e($_SESSION['full_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isCitizen()): ?>
                                <li><a class="dropdown-item" href="<?= basePath('citizen/profile.php') ?>"><?= e(__('profile')) ?></a></li>
                                <li><a class="dropdown-item" href="<?= basePath('citizen/change-password.php') ?>"><?= e(__('change_password')) ?></a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= basePath('auth/logout.php') ?>"><?= e(__('logout')) ?></a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= basePath('auth/login.php') ?>"><?= e(__('login')) ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= basePath('auth/register.php') ?>"><?= e(__('register')) ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
