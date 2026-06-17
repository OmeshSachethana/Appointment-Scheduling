<?php
require_once __DIR__ . '/../../includes/auth.php';
requireCitizen();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT * FROM service_requests WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$userId]);
$requests = $stmt->fetchAll();

$pageTitle = __('my_requests');
include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= e(__('my_requests')) ?></h2>
    <a href="<?= basePath('citizen/service-requests/create.php') ?>" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> <?= e(__('submit_request')) ?>
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?= e(__('request_type')) ?></th>
                        <th><?= e(__('description')) ?></th>
                        <th><?= e(__('document')) ?></th>
                        <th><?= e(__('status')) ?></th>
                        <th><?= e(__('remarks')) ?></th>
                        <th><?= e(__('date')) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4"><?= e(__('no_records')) ?></td></tr>
                <?php else: foreach ($requests as $r): ?>
                    <tr>
                        <td><?= e($r['id']) ?></td>
                        <td><?= e(serviceLabel($r['request_type'])) ?></td>
                        <td><?= e(mb_strimwidth($r['description'], 0, 50, '...')) ?></td>
                        <td>
                            <?php if ($r['document_path']): ?>
                                <a href="<?= basePath('uploads/' . $r['document_path']) ?>" target="_blank"><i class="bi bi-file-earmark"></i></a>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td><?= statusBadge($r['status']) ?></td>
                        <td><?= e($r['remarks'] ?? '—') ?></td>
                        <td><?= e(date('Y-m-d', strtotime($r['created_at']))) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
