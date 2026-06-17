<?php
require_once __DIR__ . '/../../includes/auth.php';
requireCitizen();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC');
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();

$pageTitle = __('my_appointments');
include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= e(__('my_appointments')) ?></h2>
    <a href="<?= basePath('citizen/appointments/create.php') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> <?= e(__('create_appointment')) ?>
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?= e(__('service_type')) ?></th>
                        <th><?= e(__('appointment_date')) ?></th>
                        <th><?= e(__('appointment_time')) ?></th>
                        <th><?= e(__('purpose')) ?></th>
                        <th><?= e(__('status')) ?></th>
                        <th><?= e(__('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($appointments)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4"><?= e(__('no_records')) ?></td></tr>
                <?php else: foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= e($a['id']) ?></td>
                        <td><?= e(serviceLabel($a['service_type'])) ?></td>
                        <td><?= e($a['appointment_date']) ?></td>
                        <td><?= e(substr($a['appointment_time'], 0, 5)) ?></td>
                        <td><?= e(mb_strimwidth($a['purpose'], 0, 40, '...')) ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                        <td>
                            <?php if ($a['status'] === 'pending'): ?>
                                <a href="<?= basePath('citizen/appointments/edit.php?id=' . $a['id']) ?>" class="btn btn-sm btn-outline-primary"><?= e(__('edit')) ?></a>
                                <a href="<?= basePath('citizen/appointments/cancel.php?id=' . $a['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this appointment?')"><?= e(__('cancel')) ?></a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
