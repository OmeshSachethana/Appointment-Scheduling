<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $id = (int) $_POST['request_id'];
    $action = $_POST['action'];
    $remarks = trim($_POST['remarks'] ?? '');

    $allowed = ['processing', 'approved', 'rejected', 'completed', 'documents_required'];
    if (in_array($action, $allowed, true)) {
        $stmt = $pdo->prepare('SELECT * FROM service_requests WHERE id = ?');
        $stmt->execute([$id]);
        $req = $stmt->fetch();

        if ($req) {
            if ($action === 'documents_required') {
                $status = 'processing';
                $remarks = $remarks ?: __('documents_required');
            } else {
                $status = $action;
            }

            $stmt = $pdo->prepare('UPDATE service_requests SET status = ?, remarks = ? WHERE id = ?');
            $stmt->execute([$status, $remarks ?: $req['remarks'], $id]);

            if ($action === 'documents_required') {
                $msg = __('service_requests') . ' #' . $id . ' - ' . __('documents_required');
            } elseif ($action === 'completed') {
                $msg = __('service_requests') . ' #' . $id . ' - ' . __('status_completed');
            } else {
                $msg = __('service_requests') . ' #' . $id . ' - ' . __('status_' . $status);
            }
            if ($remarks) {
                $msg .= ': ' . $remarks;
            }
            createNotification($pdo, (int) $req['user_id'], $msg);
            setFlash('success', __('success'));
        }
    }
    redirect(basePath('admin/service-requests.php'));
}

$filter = $_GET['status'] ?? '';
$sql = 'SELECT sr.*, u.full_name, u.email FROM service_requests sr JOIN users u ON sr.user_id = u.id';
$params = [];
if ($filter !== '' && in_array($filter, REQUEST_STATUSES, true)) {
    $sql .= ' WHERE sr.status = ?';
    $params[] = $filter;
}
$sql .= ' ORDER BY sr.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

$pageTitle = __('manage_requests');
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4"><?= e(__('manage_requests')) ?></h2>

<div class="mb-3 filter-pills">
    <a href="?" class="btn btn-sm btn-outline-secondary"><?= e(__('all_requests')) ?></a>
    <?php foreach (REQUEST_STATUSES as $s): ?>
        <a href="?status=<?= $s ?>" class="btn btn-sm btn-outline-primary <?= $filter === $s ? 'active' : '' ?>"><?= e(__('status_' . $s)) ?></a>
    <?php endforeach; ?>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?= e(__('full_name')) ?></th>
                        <th><?= e(__('request_type')) ?></th>
                        <th><?= e(__('description')) ?></th>
                        <th><?= e(__('document')) ?></th>
                        <th><?= e(__('status')) ?></th>
                        <th><?= e(__('remarks')) ?></th>
                        <th><?= e(__('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4"><?= e(__('no_records')) ?></td></tr>
                <?php else: foreach ($requests as $r): ?>
                    <tr>
                        <td><?= e($r['id']) ?></td>
                        <td><?= e($r['full_name']) ?></td>
                        <td><?= e(serviceLabel($r['request_type'])) ?></td>
                        <td><?= e(mb_strimwidth($r['description'], 0, 40, '...')) ?></td>
                        <td>
                            <?php if ($r['document_path']): ?>
                                <a href="<?= basePath('uploads/' . $r['document_path']) ?>" target="_blank"><i class="bi bi-download"></i></a>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td><?= statusBadge($r['status']) ?></td>
                        <td><?= e($r['remarks'] ?? '—') ?></td>
                        <td>
                            <?php if (!in_array($r['status'], ['completed', 'rejected'], true)): ?>
                            <form method="POST" class="d-flex flex-column gap-1" style="min-width:180px">
                                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                <input type="text" name="remarks" class="form-control form-control-sm" placeholder="<?= e(__('remarks')) ?>">
                                <div class="btn-group btn-group-sm">
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <button name="action" value="processing" class="btn btn-info"><?= e(__('process')) ?></button>
                                    <?php endif; ?>
                                    <button name="action" value="approved" class="btn btn-success"><?= e(__('approve')) ?></button>
                                    <button name="action" value="rejected" class="btn btn-danger"><?= e(__('reject')) ?></button>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button name="action" value="documents_required" class="btn btn-warning"><?= e(__('documents_required')) ?></button>
                                    <button name="action" value="completed" class="btn btn-primary"><?= e(__('complete')) ?></button>
                                </div>
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
