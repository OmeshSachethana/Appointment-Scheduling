<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['action'])) {
    $id = (int) $_POST['appointment_id'];
    $action = $_POST['action'];
    $allowed = ['approved', 'rejected', 'completed'];

    if (in_array($action, $allowed, true)) {
        $stmt = $pdo->prepare('SELECT * FROM appointments WHERE id = ?');
        $stmt->execute([$id]);
        $appt = $stmt->fetch();

        if ($appt) {
            $stmt = $pdo->prepare('UPDATE appointments SET status = ? WHERE id = ?');
            $stmt->execute([$action, $id]);
            $msg = __('appointments') . ' #' . $id . ' - ' . __('status_' . $action);
            createNotification($pdo, (int) $appt['user_id'], $msg);
            setFlash('success', __('success'));
        }
    }
    redirect(basePath('admin/appointments.php'));
}

$filter = $_GET['status'] ?? '';
$sql = 'SELECT a.*, u.full_name, u.email FROM appointments a JOIN users u ON a.user_id = u.id';
$params = [];
if ($filter !== '' && in_array($filter, APPOINTMENT_STATUSES, true)) {
    $sql .= ' WHERE a.status = ?';
    $params[] = $filter;
}
$sql .= ' ORDER BY a.appointment_date DESC, a.appointment_time DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

$pageTitle = __('manage_appointments');
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4"><?= e(__('manage_appointments')) ?></h2>

<div class="mb-3">
    <a href="?" class="btn btn-sm btn-outline-secondary"><?= e(__('all_appointments')) ?></a>
    <?php foreach (['pending', 'approved', 'rejected', 'completed', 'cancelled'] as $s): ?>
        <a href="?status=<?= $s ?>" class="btn btn-sm btn-outline-primary <?= $filter === $s ? 'active' : '' ?>"><?= e(__('status_' . $s)) ?></a>
    <?php endforeach; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?= e(__('full_name')) ?></th>
                        <th><?= e(__('service_type')) ?></th>
                        <th><?= e(__('date')) ?></th>
                        <th><?= e(__('time')) ?></th>
                        <th><?= e(__('purpose')) ?></th>
                        <th><?= e(__('status')) ?></th>
                        <th><?= e(__('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($appointments)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4"><?= e(__('no_records')) ?></td></tr>
                <?php else: foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= e($a['id']) ?></td>
                        <td><?= e($a['full_name']) ?><br><small class="text-muted"><?= e($a['email']) ?></small></td>
                        <td><?= e(serviceLabel($a['service_type'])) ?></td>
                        <td><?= e($a['appointment_date']) ?></td>
                        <td><?= e(substr($a['appointment_time'], 0, 5)) ?></td>
                        <td><?= e(mb_strimwidth($a['purpose'], 0, 30, '...')) ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                        <td>
                            <?php if ($a['status'] === 'pending'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                    <button name="action" value="approved" class="btn btn-sm btn-success"><?= e(__('approve')) ?></button>
                                    <button name="action" value="rejected" class="btn btn-sm btn-danger"><?= e(__('reject')) ?></button>
                                </form>
                            <?php elseif ($a['status'] === 'approved'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                    <button name="action" value="completed" class="btn btn-sm btn-primary"><?= e(__('complete')) ?></button>
                                </form>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
