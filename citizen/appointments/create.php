<?php
require_once __DIR__ . '/../../includes/auth.php';
requireCitizen();

$pdo = getDBConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceType = $_POST['service_type'] ?? '';
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';
    $purpose = trim($_POST['purpose'] ?? '');

    if (!in_array($serviceType, SERVICE_TYPES, true) || $date === '' || $time === '' || $purpose === '') {
        $errors[] = __('field_required');
    } elseif ($date < date('Y-m-d')) {
        $errors[] = __('date') . ' must be in the future';
    } else {
        $stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_type, appointment_date, appointment_time, purpose) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $serviceType, $date, $time, $purpose]);
        setFlash('success', __('appointment_created'));
        redirect(basePath('citizen/appointments/index.php'));
    }
}

$pageTitle = __('create_appointment');
include __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header"><h5 class="mb-0"><?= e(__('create_appointment')) ?></h5></div>
            <div class="card-body">
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('service_type')) ?></label>
                        <select name="service_type" class="form-select" required>
                            <option value=""><?= e(__('select')) ?>...</option>
                            <?php foreach (SERVICE_TYPES as $type): ?>
                                <option value="<?= e($type) ?>" <?= ($_POST['service_type'] ?? '') === $type ? 'selected' : '' ?>><?= e(serviceLabel($type)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('appointment_date')) ?></label>
                            <input type="date" name="appointment_date" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= e($_POST['appointment_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('appointment_time')) ?></label>
                            <input type="time" name="appointment_time" class="form-control" required min="08:00" max="16:00" value="<?= e($_POST['appointment_time'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('purpose')) ?></label>
                        <textarea name="purpose" class="form-control" rows="3" required><?= e($_POST['purpose'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= e(__('submit')) ?></button>
                    <a href="<?= basePath('citizen/appointments/index.php') ?>" class="btn btn-outline-secondary"><?= e(__('back')) ?></a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
