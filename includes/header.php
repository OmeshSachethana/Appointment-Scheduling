<?php
if (!isset($pageTitle)) {
    $pageTitle = APP_SHORT;
}
$currentLang = $_SESSION['lang'] ?? 'en';

$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$bodyClass = 'app-body';
if (str_contains($scriptPath, '/auth/')) {
    $bodyClass .= ' page-auth';
}
if (preg_match('#/appointment-scheduling/?$#', $scriptPath) || str_ends_with($scriptPath, '/index.php') && !str_contains($scriptPath, '/citizen/') && !str_contains($scriptPath, '/admin/')) {
    $bodyClass .= ' page-home';
}
?>
<!DOCTYPE html>
<html lang="<?= e($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f2744">
    <title><?= e($pageTitle) ?> | <?= e(__('app_short')) ?></title>
    <link rel="icon" href="<?= logoPath() ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= basePath('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="<?= e($bodyClass) ?>">
<?php include __DIR__ . '/navbar.php'; ?>
<main class="main-content">
    <div class="container py-4">
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
