<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = getDBConnection();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($userId <= 0 || $message === '') {
        $errors[] = __('field_required');
    } else {
        createNotification($pdo, $userId, $message);
        setFlash('success', __('notification_sent'));
        redirect(basePath('admin/send-notification.php'));
    }
}

$users = $pdo->query("SELECT id, full_name, email FROM users WHERE role = 'citizen' ORDER BY full_name")->fetchAll();

$pageTitle = __('send_notifications');
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4"><?= e(__('send_notifications')) ?></h2>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('users')) ?></label>
                        <select name="user_id" class="form-select" required>
                            <option value=""><?= e(__('select')) ?>...</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= e($u['full_name']) ?> (<?= e($u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('notifications')) ?></label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning"><?= e(__('send_notification')) ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
