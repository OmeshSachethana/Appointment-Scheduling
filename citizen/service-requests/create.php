<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireCitizen();

$pdo = getDBConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestType = $_POST['request_type'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (!in_array($requestType, SERVICE_TYPES, true) || $description === '') {
        $errors[] = __('field_required');
    } else {
        $docPath = null;
        if (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = handleUpload($_FILES['document']);
            if ($upload === false) {
                $errors[] = __('error') . ' (invalid file)';
            } else {
                $docPath = $upload;
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('INSERT INTO service_requests (user_id, request_type, description, document_path) VALUES (?, ?, ?, ?)');
            $stmt->execute([$_SESSION['user_id'], $requestType, $description, $docPath]);
            setFlash('success', __('request_submitted'));
            redirect(basePath('citizen/service-requests/index.php'));
        }
    }
}

$pageTitle = __('submit_request');
include __DIR__ . '/../../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header"><h5 class="mb-0"><?= e(__('submit_request')) ?></h5></div>
            <div class="card-body">
                <?php foreach ($errors as $err): ?>
                    <div class="alert alert-danger"><?= e($err) ?></div>
                <?php endforeach; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('request_type')) ?></label>
                        <select name="request_type" class="form-select" required>
                            <option value=""><?= e(__('select')) ?>...</option>
                            <?php foreach (SERVICE_TYPES as $type): ?>
                                <option value="<?= e($type) ?>"><?= e(serviceLabel($type)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('description')) ?></label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="<?= e(__('description')) ?>"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= e(__('upload_document')) ?></label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="text-muted">PDF, JPG, PNG, DOC (max 5MB)</small>
                    </div>
                    <button type="submit" class="btn btn-success"><?= e(__('submit')) ?></button>
                    <a href="<?= basePath('citizen/service-requests/index.php') ?>" class="btn btn-outline-secondary"><?= e(__('back')) ?></a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
