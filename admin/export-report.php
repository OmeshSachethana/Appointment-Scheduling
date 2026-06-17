<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../lib/SimplePdf.php';
requireAdmin();

$pdo = getDBConnection();
$reportType = $_GET['type'] ?? 'monthly';
$month = (int) ($_GET['month'] ?? date('n'));
$year = (int) ($_GET['year'] ?? date('Y'));

$pdf = new SimplePdf();
$pdf->setTitle(__('app_short') . ' - Report');
$pdf->addText('Generated: ' . date('Y-m-d H:i:s'));
$pdf->addText('');

switch ($reportType) {
    case 'monthly':
        $pdf->addText(__('report_monthly_appointments') . " ({$month}/{$year})", 13, true);
        $stmt = $pdo->prepare("
            SELECT a.id, u.full_name, a.service_type, a.appointment_date, a.appointment_time, a.status
            FROM appointments a JOIN users u ON a.user_id = u.id
            WHERE MONTH(a.appointment_date) = ? AND YEAR(a.appointment_date) = ?
            ORDER BY a.appointment_date
        ");
        $stmt->execute([$month, $year]);
        $rows = $stmt->fetchAll();
        $pdf->addTable(
            ['ID', 'Name', 'Service', 'Date', 'Time', 'Status'],
            array_map(fn($r) => [
                $r['id'], $r['full_name'], serviceLabel($r['service_type']),
                $r['appointment_date'], substr($r['appointment_time'], 0, 5), $r['status']
            ], $rows)
        );
        $filename = "monthly_appointments_{$year}_{$month}.pdf";
        break;

    case 'stats':
        $pdf->addText(__('report_request_stats'), 13, true);
        $data = $pdo->query("SELECT request_type, status, COUNT(*) AS total FROM service_requests GROUP BY request_type, status")->fetchAll();
        $pdf->addTable(
            ['Type', 'Status', 'Count'],
            array_map(fn($r) => [serviceLabel($r['request_type']), $r['status'], $r['total']], $data)
        );
        $filename = 'request_statistics.pdf';
        break;

    case 'completed':
        $pdf->addText(__('report_completed'), 13, true);
        $data = $pdo->query("SELECT sr.id, u.full_name, sr.request_type, sr.created_at FROM service_requests sr JOIN users u ON sr.user_id = u.id WHERE sr.status = 'completed'")->fetchAll();
        $pdf->addTable(
            ['ID', 'Name', 'Type', 'Date'],
            array_map(fn($r) => [$r['id'], $r['full_name'], serviceLabel($r['request_type']), date('Y-m-d', strtotime($r['created_at']))], $data)
        );
        $filename = 'completed_requests.pdf';
        break;

    case 'pending':
        $pdf->addText(__('report_pending'), 13, true);
        $data = $pdo->query("SELECT sr.id, u.full_name, sr.request_type, sr.status, sr.created_at FROM service_requests sr JOIN users u ON sr.user_id = u.id WHERE sr.status IN ('pending','processing')")->fetchAll();
        $pdf->addTable(
            ['ID', 'Name', 'Type', 'Status', 'Date'],
            array_map(fn($r) => [$r['id'], $r['full_name'], serviceLabel($r['request_type']), $r['status'], date('Y-m-d', strtotime($r['created_at']))], $data)
        );
        $filename = 'pending_requests.pdf';
        break;

    default:
        $filename = 'report.pdf';
        $pdf->addText(__('no_records'));
}

$pdf->output($filename);
