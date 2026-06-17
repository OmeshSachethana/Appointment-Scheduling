<?php
require_once __DIR__ . '/../../includes/auth.php';
requireCitizen();

$pdo = getDBConnection();
$user = getCurrentUser($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $language = in_array($_POST['language'] ?? 'en', ['en', 'si'], true) ? $_POST['language'] : 'en';

    if ($fullName === '' || $phone === '' || $address === '') {
        $errors[] = __('field_required');
    } else {
        $stmt = $pdo->prepare('UPDATE users SET full_name = ?, phone_number = ?, address = ?, language = ? WHERE id = ?');
        $stmt->execute([$fullName, $phone, $address, $language, $_SESSION['user_id']]);
        $_SESSION['full_name'] = $fullName;
        $_SESSION['lang'] = $language;
        setFlash('success', __('profile_updated'));
        redirect(basePath('citizen/profile.php'));
    }
}

$pageTitle = __('profile');
include __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header"><h5 class="mb-0"><?= e(__('profile')) ?></h5></div>
            <div class="card-body">
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('nic')) ?></label>
                            <input type="text" class="form-control" value="<?= e($user['nic']) ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('email')) ?></label>
                            <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('full_name')) ?></label>
                            <input type="text" name="full_name" class="form-control" required value="<?= e($_POST['full_name'] ?? $user['full_name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('phone_number')) ?></label>
                            <input type="text" name="phone_number" class="form-control" required value="<?= e($_POST['phone_number'] ?? $user['phone_number']) ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label"><?= e(__('address')) ?></label>
                            <textarea name="address" class="form-control" rows="2" required><?= e($_POST['address'] ?? $user['address']) ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('language')) ?></label>
                            <select name="language" class="form-select">
                                <option value="en" <?= ($user['language'] ?? 'en') === 'en' ? 'selected' : '' ?>><?= e(__('english')) ?></option>
                                <option value="si" <?= ($user['language'] ?? '') === 'si' ? 'selected' : '' ?>><?= e(__('sinhala')) ?></option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= e(__('save')) ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
