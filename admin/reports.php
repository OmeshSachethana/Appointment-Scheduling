<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = getDBConnection();
$reportType = $_GET['type'] ?? 'monthly';
$month = (int) ($_GET['month'] ?? date('n'));
$year = (int) ($_GET['year'] ?? date('Y'));

$data = [];
$title = '';

switch ($reportType) {
    case 'monthly':
        $title = __('report_monthly_appointments');
        $stmt = $pdo->prepare("
            SELECT a.*, u.full_name FROM appointments a
            JOIN users u ON a.user_id = u.id
            WHERE MONTH(a.appointment_date) = ? AND YEAR(a.appointment_date) = ?
            ORDER BY a.appointment_date
        ");
        $stmt->execute([$month, $year]);
        $data = $stmt->fetchAll();
        break;
    case 'stats':
        $title = __('report_request_stats');
        $data = $pdo->query("
            SELECT request_type, status, COUNT(*) AS total
            FROM service_requests GROUP BY request_type, status ORDER BY request_type
        ")->fetchAll();
        break;
    case 'completed':
        $title = __('report_completed');
        $data = $pdo->query("
            SELECT sr.*, u.full_name FROM service_requests sr
            JOIN users u ON sr.user_id = u.id
            WHERE sr.status = 'completed' ORDER BY sr.created_at DESC
        ")->fetchAll();
        break;
    case 'pending':
        $title = __('report_pending');
        $data = $pdo->query("
            SELECT sr.*, u.full_name FROM service_requests sr
            JOIN users u ON sr.user_id = u.id
            WHERE sr.status IN ('pending', 'processing') ORDER BY sr.created_at DESC
        ")->fetchAll();
        break;
    default:
        $reportType = 'monthly';
        $title = __('report_monthly_appointments');
}

$pageTitle = __('reports');
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4"><?= e(__('view_reports')) ?></h2>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-header"><?= e(__('generate_report')) ?></div>
            <div class="list-group list-group-flush">
                <a href="?type=monthly" class="list-group-item list-group-item-action <?= $reportType === 'monthly' ? 'active' : '' ?>"><?= e(__('report_monthly_appointments')) ?></a>
                <a href="?type=stats" class="list-group-item list-group-item-action <?= $reportType === 'stats' ? 'active' : '' ?>"><?= e(__('report_request_stats')) ?></a>
                <a href="?type=completed" class="list-group-item list-group-item-action <?= $reportType === 'completed' ? 'active' : '' ?>"><?= e(__('report_completed')) ?></a>
                <a href="?type=pending" class="list-group-item list-group-item-action <?= $reportType === 'pending' ? 'active' : '' ?>"><?= e(__('report_pending')) ?></a>
            </div>
        </div>
        <?php if ($reportType === 'monthly'): ?>
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body">
                <form method="GET">
                    <input type="hidden" name="type" value="monthly">
                    <div class="mb-2">
                        <label class="form-label"><?= e(__('month')) ?></label>
                        <select name="month" class="form-select">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $month === $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label"><?= e(__('year')) ?></label>
                        <input type="number" name="year" class="form-control" value="<?= $year ?>" min="2020" max="2030">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"><?= e(__('filter')) ?></button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><?= e($title) ?></span>
                <a href="<?= basePath('admin/export-report.php?type=' . urlencode($reportType) . '&month=' . $month . '&year=' . $year) ?>" class="btn btn-sm btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> <?= e(__('export_pdf')) ?>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <?php if ($reportType === 'monthly'): ?>
                    <table class="table table-striped mb-0">
                        <thead><tr><th>#</th><th><?= e(__('full_name')) ?></th><th><?= e(__('service_type')) ?></th><th><?= e(__('date')) ?></th><th><?= e(__('time')) ?></th><th><?= e(__('status')) ?></th></tr></thead>
                        <tbody>
                        <?php if (empty($data)): ?>
                            <tr><td colspan="6" class="text-center py-4"><?= e(__('no_records')) ?></td></tr>
                        <?php else: foreach ($data as $row): ?>
                            <tr>
                                <td><?= e($row['id']) ?></td>
                                <td><?= e($row['full_name']) ?></td>
                                <td><?= e(serviceLabel($row['service_type'])) ?></td>
                                <td><?= e($row['appointment_date']) ?></td>
                                <td><?= e(substr($row['appointment_time'], 0, 5)) ?></td>
                                <td><?= statusBadge($row['status']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    <?php elseif ($reportType === 'stats'): ?>
                    <table class="table table-striped mb-0">
                        <thead><tr><th><?= e(__('request_type')) ?></th><th><?= e(__('status')) ?></th><th><?= e(__('count')) ?></th></tr></thead>
                        <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= e(serviceLabel($row['request_type'])) ?></td>
                                <td><?= statusBadge($row['status']) ?></td>
                                <td><?= e($row['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <table class="table table-striped mb-0">
                        <thead><tr><th>#</th><th><?= e(__('full_name')) ?></th><th><?= e(__('request_type')) ?></th><th><?= e(__('status')) ?></th><th><?= e(__('date')) ?></th></tr></thead>
                        <tbody>
                        <?php if (empty($data)): ?>
                            <tr><td colspan="5" class="text-center py-4"><?= e(__('no_records')) ?></td></tr>
                        <?php else: foreach ($data as $row): ?>
                            <tr>
                                <td><?= e($row['id']) ?></td>
                                <td><?= e($row['full_name']) ?></td>
                                <td><?= e(serviceLabel($row['request_type'])) ?></td>
                                <td><?= statusBadge($row['status']) ?></td>
                                <td><?= e(date('Y-m-d', strtotime($row['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer text-muted">
                <?= e(__('total')) ?>: <?= count($data) ?> <?= e(__('count')) ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
