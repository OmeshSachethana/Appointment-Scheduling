<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? basePath('admin/dashboard.php') : basePath('citizen/dashboard.php'));
}

$pageTitle = __('register_title');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nic = trim($_POST['nic'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $language = in_array($_POST['language'] ?? 'en', ['en', 'si'], true) ? $_POST['language'] : 'en';

    if ($nic === '' || $fullName === '' || $email === '' || $phone === '' || $address === '' || $password === '') {
        $errors[] = __('field_required');
    }
    if (!validateNIC($nic)) {
        $errors[] = __('invalid_nic');
    }
    if ($password !== $confirm) {
        $errors[] = __('password_mismatch');
    }
    if (strlen($password) < 6) {
        $errors[] = __('password') . ' (min 6 characters)';
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR nic = ?');
        $stmt->execute([$email, $nic]);
        if ($stmt->fetch()) {
            $errors[] = __('email_exists');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (nic, full_name, email, phone_number, address, password, role, language) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nic, $fullName, $email, $phone, $address, $hash, 'citizen', $language]);
            setFlash('success', __('register_success'));
            redirect(basePath('auth/login.php'));
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <?php include __DIR__ . '/../includes/auth-brand.php'; ?>
    <div class="card auth-card auth-card--wide">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i><?= e(__('register_title')) ?></h4>
        </div>
        <div class="card-body p-4">
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('nic')) ?></label>
                            <input type="text" name="nic" class="form-control" required maxlength="12" value="<?= e($_POST['nic'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('full_name')) ?></label>
                            <input type="text" name="full_name" class="form-control" required value="<?= e($_POST['full_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('email')) ?></label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('phone_number')) ?></label>
                            <input type="text" name="phone_number" class="form-control" required value="<?= e($_POST['phone_number'] ?? '') ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label"><?= e(__('address')) ?></label>
                            <textarea name="address" class="form-control" rows="2" required><?= e($_POST['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?= e(__('password')) ?></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?= e(__('confirm_password')) ?></label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?= e(__('language')) ?></label>
                            <select name="language" class="form-select">
                                <option value="en"><?= e(__('english')) ?></option>
                                <option value="si"><?= e(__('sinhala')) ?></option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-lg me-1"></i><?= e(__('register')) ?></button>
                    <a href="<?= basePath('auth/login.php') ?>" class="btn btn-outline-secondary"><?= e(__('login_here')) ?></a>
                </form>
            </div>
        </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
