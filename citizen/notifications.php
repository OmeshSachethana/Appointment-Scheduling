<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

if (isset($_GET['read']) && (int) $_GET['read'] > 0) {
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([(int) $_GET['read'], $userId]);
    redirect(basePath(isAdmin() ? 'admin/notifications.php' : 'citizen/notifications.php'));
}

if (isset($_GET['read_all'])) {
    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
    $stmt->execute([$userId]);
    redirect(basePath(isAdmin() ? 'admin/notifications.php' : 'citizen/notifications.php'));
}

$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll();

$pageTitle = __('notifications');
include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= e(__('notifications')) ?></h2>
    <?php if (!empty($notifications)): ?>
        <a href="?read_all=1" class="btn btn-outline-primary btn-sm"><?= e(__('mark_all_read')) ?></a>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="list-group list-group-flush">
        <?php if (empty($notifications)): ?>
            <div class="list-group-item text-center text-muted py-5"><?= e(__('no_records')) ?></div>
        <?php else: foreach ($notifications as $n): ?>
            <div class="list-group-item <?= $n['is_read'] ? '' : 'list-group-item-primary' ?>">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1"><?= e($n['message']) ?></p>
                        <small class="text-muted"><?= e(date('Y-m-d H:i', strtotime($n['created_at']))) ?></small>
                    </div>
                    <?php if (!$n['is_read']): ?>
                        <a href="?read=<?= $n['id'] ?>" class="btn btn-sm btn-outline-primary"><?= e(__('mark_read')) ?></a>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?= e(__('read')) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
