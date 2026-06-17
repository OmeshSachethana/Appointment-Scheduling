<?php
require_once __DIR__ . '/../../includes/auth.php';
requireCitizen();

$id = (int) ($_GET['id'] ?? 0);
$pdo = getDBConnection();

$stmt = $pdo->prepare('SELECT * FROM appointments WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$appointment = $stmt->fetch();

if (!$appointment || $appointment['status'] !== 'pending') {
    setFlash('danger', __('error'));
    redirect(basePath('citizen/appointments/index.php'));
}

$stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
$stmt->execute([$id]);

createNotification($pdo, $_SESSION['user_id'], __('appointments') . ' #' . $id . ' - ' . __('status_cancelled'));
setFlash('success', __('appointment_cancelled'));
redirect(basePath('citizen/appointments/index.php'));
