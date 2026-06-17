<?php
require_once __DIR__ . '/../includes/auth.php';
requireCitizen();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = ?');
$stmt->execute([$userId]);
$totalAppointments = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$userId]);
$pendingAppointments = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM service_requests WHERE user_id = ?');
$stmt->execute([$userId]);
$totalRequests = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM service_requests WHERE user_id = ? AND status IN ('pending', 'processing')");
$stmt->execute([$userId]);
$activeRequests = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT * FROM appointments WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([$userId]);
$recentAppointments = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM service_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([$userId]);
$recentRequests = $stmt->fetchAll();

$pageTitle = __('dashboard');
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4"><?= e(__('welcome')) ?>, <?= e($_SESSION['full_name']) ?>!</h2>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card stat-card--primary shadow-sm">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="stat-label"><?= e(__('appointments')) ?></div>
                <div class="stat-value"><?= $totalAppointments ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card stat-card--warning shadow-sm">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-label"><?= e(__('pending_appointments')) ?></div>
                <div class="stat-value"><?= $pendingAppointments ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card stat-card--success shadow-sm">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="stat-label"><?= e(__('service_requests')) ?></div>
                <div class="stat-value"><?= $totalRequests ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card stat-card--info shadow-sm">
            <div class="card-body">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-label"><?= e(__('pending_requests')) ?></div>
                <div class="stat-value"><?= $activeRequests ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><?= e(__('recent_appointments')) ?></span>
                <a href="<?= basePath('citizen/appointments/create.php') ?>" class="btn btn-sm btn-primary"><?= e(__('create_appointment')) ?></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th><?= e(__('date')) ?></th><th><?= e(__('service_type')) ?></th><th><?= e(__('status')) ?></th></tr></thead>
                        <tbody>
                        <?php if (empty($recentAppointments)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3"><?= e(__('no_records')) ?></td></tr>
                        <?php else: foreach ($recentAppointments as $a): ?>
                            <tr>
                                <td><?= e($a['appointment_date']) ?></td>
                                <td><?= e(serviceLabel($a['service_type'])) ?></td>
                                <td><?= statusBadge($a['status']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><?= e(__('recent_requests')) ?></span>
                <a href="<?= basePath('citizen/service-requests/create.php') ?>" class="btn btn-sm btn-success"><?= e(__('submit_request')) ?></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th><?= e(__('request_type')) ?></th><th><?= e(__('date')) ?></th><th><?= e(__('status')) ?></th></tr></thead>
                        <tbody>
                        <?php if (empty($recentRequests)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3"><?= e(__('no_records')) ?></td></tr>
                        <?php else: foreach ($recentRequests as $r): ?>
                            <tr>
                                <td><?= e(serviceLabel($r['request_type'])) ?></td>
                                <td><?= e(date('Y-m-d', strtotime($r['created_at']))) ?></td>
                                <td><?= statusBadge($r['status']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
