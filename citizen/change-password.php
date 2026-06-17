<?php
require_once __DIR__ . '/../includes/auth.php';
requireCitizen();

$pdo = getDBConnection();
$user = getCurrentUser($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($current === '' || $newPass === '' || $confirm === '') {
        $errors[] = __('field_required');
    } elseif (!password_verify($current, $user['password'])) {
        $errors[] = __('wrong_password');
    } elseif ($newPass !== $confirm) {
        $errors[] = __('password_mismatch');
    } elseif (strlen($newPass) < 6) {
        $errors[] = __('password') . ' (min 6 characters)';
    } else {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hash, $_SESSION['user_id']]);
        setFlash('success', __('password_changed'));
        redirect(basePath('citizen/change-password.php'));
    }
}

$pageTitle = __('change_password');
include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header"><h5 class="mb-0"><?= e(__('change_password')) ?></h5></div>
            <div class="card-body">
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('current_password')) ?></label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('new_password')) ?></label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('confirm_password')) ?></label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary"><?= e(__('save')) ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
