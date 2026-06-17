<?php
if (!isset($pageTitle)) {
    $pageTitle = APP_SHORT;
}
$currentLang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= e($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(__('app_short')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= basePath('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="main-content">
    <div class="container py-4">
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
