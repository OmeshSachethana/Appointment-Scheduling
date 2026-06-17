<?php
require_once __DIR__ . '/../../includes/auth.php';
requireCitizen();

$id = (int) ($_GET['id'] ?? 0);
$pdo = getDBConnection();
$errors = [];

$stmt = $pdo->prepare('SELECT * FROM appointments WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    setFlash('danger', __('error'));
    redirect(basePath('citizen/appointments/index.php'));
}

if ($appointment['status'] !== 'pending') {
    setFlash('warning', __('pending_only_edit'));
    redirect(basePath('citizen/appointments/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceType = $_POST['service_type'] ?? '';
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';
    $purpose = trim($_POST['purpose'] ?? '');

    if (!in_array($serviceType, SERVICE_TYPES, true) || $date === '' || $time === '' || $purpose === '') {
        $errors[] = __('field_required');
    } else {
        $stmt = $pdo->prepare('UPDATE appointments SET service_type = ?, appointment_date = ?, appointment_time = ?, purpose = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$serviceType, $date, $time, $purpose, $id, $_SESSION['user_id']]);
        setFlash('success', __('appointment_updated'));
        redirect(basePath('citizen/appointments/index.php'));
    }
}

$pageTitle = __('edit_appointment');
include __DIR__ . '/../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header"><h5 class="mb-0"><?= e(__('edit_appointment')) ?></h5></div>
            <div class="card-body">
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('service_type')) ?></label>
                        <select name="service_type" class="form-select" required>
                            <?php foreach (SERVICE_TYPES as $type): ?>
                                <option value="<?= e($type) ?>" <?= $appointment['service_type'] === $type ? 'selected' : '' ?>><?= e(serviceLabel($type)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('appointment_date')) ?></label>
                            <input type="date" name="appointment_date" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= e($appointment['appointment_date']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= e(__('appointment_time')) ?></label>
                            <input type="time" name="appointment_time" class="form-control" required value="<?= e(substr($appointment['appointment_time'], 0, 5)) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('purpose')) ?></label>
                        <textarea name="purpose" class="form-control" rows="3" required><?= e($appointment['purpose']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= e(__('save')) ?></button>
                    <a href="<?= basePath('citizen/appointments/index.php') ?>" class="btn btn-outline-secondary"><?= e(__('back')) ?></a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
