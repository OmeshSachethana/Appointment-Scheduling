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

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="bi bi-shield-lock"></i> <?= e(__('admin_login')) ?></h4>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('email')) ?></label>
                        <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('password')) ?></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100"><?= e(__('login')) ?></button>
                </form>
                <p class="mt-3 mb-0 text-center">
                    <a href="<?= basePath('auth/login.php') ?>"><?= e(__('citizen_login')) ?></a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
