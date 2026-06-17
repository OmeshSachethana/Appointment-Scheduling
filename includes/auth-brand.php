<?php
/**
 * Reusable logo block for auth and setup pages.
 * @var string $logoTitle Optional title below the logo
 */
$logoTitle = $logoTitle ?? __('app_short');
?>
<div class="auth-brand">
    <div class="auth-logo-wrap" aria-hidden="true">
        <img src="<?= logoPath() ?>" alt="<?= e($logoTitle) ?>" class="auth-logo">
    </div>
    <div class="auth-brand-title"><?= e($logoTitle) ?></div>
</div>
