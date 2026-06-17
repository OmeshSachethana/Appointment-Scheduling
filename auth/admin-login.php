<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? basePath('admin/dashboard.php') : basePath('citizen/dashboard.php'));
}

$pageTitle = __('admin_login_title');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = __('field_required');
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = ?');
        $stmt->execute([$email, 'admin']);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            setFlash('success', __('login_success'));
            redirect(basePath('admin/dashboard.php'));
        } else {
            $error = __('invalid_credentials');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="card-header auth-card-header py-3">
            <h4 class="mb-0"><i class="bi bi-shield-lock me-2"></i><?= e(__('admin_login')) ?></h4>
        </div>
        <div class="card-body p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-circle me-1"></i><?= e($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><?= e(__('email')) ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= e(__('password')) ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-shield-check me-1"></i><?= e(__('login')) ?>
                </button>
            </form>
            <div class="auth-links mt-4 text-center">
                <p class="mb-0"><a href="<?= basePath('auth/login.php') ?>"><?= e(__('citizen_login')) ?></a></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
