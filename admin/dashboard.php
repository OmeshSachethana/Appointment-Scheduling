<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = getDBConnection();

$stats = [
    'citizens' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'citizen'")->fetchColumn(),
    'appointments' => (int) $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
    'pending_appt' => (int) $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn(),
    'approved_appt' => (int) $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'approved'")->fetchColumn(),
    'requests' => (int) $pdo->query('SELECT COUNT(*) FROM service_requests')->fetchColumn(),
    'pending_req' => (int) $pdo->query("SELECT COUNT(*) FROM service_requests WHERE status = 'pending'")->fetchColumn(),
    'completed_req' => (int) $pdo->query("SELECT COUNT(*) FROM service_requests WHERE status = 'completed'")->fetchColumn(),
];

$monthlyData = $pdo->query("
    SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month, COUNT(*) AS total
    FROM appointments
    WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month ORDER BY month
")->fetchAll();

$requestStats = $pdo->query("
    SELECT request_type, COUNT(*) AS total FROM service_requests GROUP BY request_type
")->fetchAll();

$recentAppt = $pdo->query("
    SELECT a.*, u.full_name FROM appointments a
    JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 5
")->fetchAll();

$pageTitle = __('dashboard');
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4"><?= e(__('dashboard')) ?> – <?= e(__('admin_portal')) ?></h2>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-primary text-white border-0 shadow-sm"><div class="card-body"><h6><?= e(__('total_citizens')) ?></h6><h2><?= $stats['citizens'] ?></h2></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-info text-white border-0 shadow-sm"><div class="card-body"><h6><?= e(__('total_appointments')) ?></h6><h2><?= $stats['appointments'] ?></h2></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-warning text-dark border-0 shadow-sm"><div class="card-body"><h6><?= e(__('pending_appointments')) ?></h6><h2><?= $stats['pending_appt'] ?></h2></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-success text-white border-0 shadow-sm"><div class="card-body"><h6><?= e(__('approved_appointments')) ?></h6><h2><?= $stats['approved_appt'] ?></h2></div></div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card stat-card bg-secondary text-white border-0 shadow-sm"><div class="card-body"><h6><?= e(__('total_requests')) ?></h6><h2><?= $stats['requests'] ?></h2></div></div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card stat-card bg-danger text-white border-0 shadow-sm"><div class="card-body"><h6><?= e(__('pending_requests')) ?></h6><h2><?= $stats['pending_req'] ?></h2></div></div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card stat-card bg-dark text-white border-0 shadow-sm"><div class="card-body"><h6><?= e(__('completed_requests')) ?></h6><h2><?= $stats['completed_req'] ?></h2></div></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header"><?= e(__('monthly_appointments')) ?></div>
            <div class="card-body"><canvas id="monthlyChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header"><?= e(__('request_statistics')) ?></div>
            <div class="card-body"><canvas id="requestChart" height="200"></canvas></div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header"><?= e(__('quick_actions')) ?></div>
    <div class="card-body d-flex flex-wrap gap-2">
        <a href="<?= basePath('admin/users.php') ?>" class="btn btn-outline-primary"><i class="bi bi-people"></i> <?= e(__('manage_users')) ?></a>
        <a href="<?= basePath('admin/appointments.php') ?>" class="btn btn-outline-info"><i class="bi bi-calendar"></i> <?= e(__('manage_appointments')) ?></a>
        <a href="<?= basePath('admin/service-requests.php') ?>" class="btn btn-outline-success"><i class="bi bi-file-text"></i> <?= e(__('manage_requests')) ?></a>
        <a href="<?= basePath('admin/reports.php') ?>" class="btn btn-outline-dark"><i class="bi bi-bar-chart"></i> <?= e(__('view_reports')) ?></a>
        <a href="<?= basePath('admin/send-notification.php') ?>" class="btn btn-outline-warning"><i class="bi bi-bell"></i> <?= e(__('send_notifications')) ?></a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header"><?= e(__('recent_appointments')) ?></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th><?= e(__('full_name')) ?></th><th><?= e(__('service_type')) ?></th><th><?= e(__('date')) ?></th><th><?= e(__('status')) ?></th></tr></thead>
                <tbody>
                <?php foreach ($recentAppt as $a): ?>
                    <tr>
                        <td><?= e($a['full_name']) ?></td>
                        <td><?= e(serviceLabel($a['service_type'])) ?></td>
                        <td><?= e($a['appointment_date']) ?></td>
                        <td><?= statusBadge($a['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($monthlyData, 'month')) ?>,
            datasets: [{ label: '<?= e(__('appointments')) ?>', data: <?= json_encode(array_map('intval', array_column($monthlyData, 'total'))) ?>, backgroundColor: '#0d6efd' }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
    new Chart(document.getElementById('requestChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map(fn($r) => serviceLabel($r['request_type']), $requestStats)) ?>,
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($requestStats, 'total'))) ?>, backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#20c997','#fd7e14','#0dcaf0','#6610f2','#6c757d'] }]
        },
        options: { responsive: true }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
