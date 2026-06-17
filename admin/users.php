<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = getDBConnection();

if (isset($_GET['delete']) && (int) $_GET['delete'] > 0) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'citizen'");
    $stmt->execute([(int) $_GET['delete']]);
    setFlash('success', __('success'));
    redirect(basePath('admin/users.php'));
}

$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM users WHERE role = 'citizen'";
$params = [];
if ($search !== '') {
    $sql .= ' AND (full_name LIKE ? OR email LIKE ? OR nic LIKE ?)';
    $params = ["%$search%", "%$search%", "%$search%"];
}
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle = __('manage_users');
include __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4"><?= e(__('manage_users')) ?></h2>

<div class="card shadow-sm border-0">
    <div class="card-header">
        <form class="row g-2" method="GET">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="<?= e(__('search')) ?>..." value="<?= e($search) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><?= e(__('search')) ?></button>
                <a href="<?= basePath('admin/users.php') ?>" class="btn btn-outline-secondary"><?= e(__('filter')) ?></a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?= e(__('nic')) ?></th>
                        <th><?= e(__('full_name')) ?></th>
                        <th><?= e(__('email')) ?></th>
                        <th><?= e(__('phone_number')) ?></th>
                        <th><?= e(__('language')) ?></th>
                        <th><?= e(__('date')) ?></th>
                        <th><?= e(__('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4"><?= e(__('no_records')) ?></td></tr>
                <?php else: foreach ($users as $u): ?>
                    <tr>
                        <td><?= e($u['id']) ?></td>
                        <td><?= e($u['nic']) ?></td>
                        <td><?= e($u['full_name']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['phone_number']) ?></td>
                        <td><?= e($u['language'] === 'si' ? __('sinhala') : __('english')) ?></td>
                        <td><?= e(date('Y-m-d', strtotime($u['created_at']))) ?></td>
                        <td>
                            <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')"><?= e(__('delete')) ?></a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
