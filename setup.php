<?php
/**
 * Database setup script.
 * Run once: http://localhost/appointment-scheduling/setup.php
 * Delete or protect this file after installation.
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        $statements = array_filter(array_map('trim', explode(';', $schema)));

        foreach ($statements as $statement) {
            if ($statement !== '' && stripos($statement, 'INSERT INTO users') === false) {
                $pdo->exec($statement);
            }
        }

        $pdo->exec('USE ' . DB_NAME);
        $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
        $citizenHash = password_hash('citizen123', PASSWORD_DEFAULT);

        $pdo->exec('DELETE FROM users');
        $stmt = $pdo->prepare('INSERT INTO users (nic, full_name, email, phone_number, address, password, role, language) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute(['199012345678', 'System Administrator', 'admin@minipe.gov.lk', '0812345678', 'Divisional Secretariat, Minipe', $adminHash, 'admin', 'en']);
        $stmt->execute(['199512345678', 'Kamal Perera', 'kamal@example.com', '0771234567', 'Minipe, Kandy', $citizenHash, 'citizen', 'si']);

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        $message = 'Database installed successfully! Admin: admin@minipe.gov.lk / admin123 | Citizen: kamal@example.com / citizen123';
    } catch (Exception $e) {
        $error = 'Setup failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSSMS Setup</title>
    <link rel="icon" href="<?= BASE_URL . LOGO_PATH ?>" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= htmlspecialchars(rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/') . '/assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="app-body page-auth">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="auth-brand mb-4">
                <div class="auth-logo-wrap" aria-hidden="true">
                    <img src="<?= BASE_URL . LOGO_PATH ?>" alt="" class="auth-logo">
                </div>
                <div class="auth-brand-title">DSSMS Minipe</div>
            </div>
            <div class="card setup-card">
                <div class="card-header auth-card-header py-3">
                    <h4 class="mb-0"><i class="bi bi-database-gear me-2"></i>DSSMS Minipe – Database Setup</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($message) ?></div>
                        <a href="index.php" class="btn btn-primary"><i class="bi bi-house me-1"></i>Go to Home</a>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <p class="text-muted">Click below to create the database, tables, and default accounts.</p>
                        <ul class="small text-muted mb-4">
                            <li>Ensure MySQL is running (XAMPP/WAMP/Laragon)</li>
                            <li>Update credentials in <code>config/database.php</code> if needed</li>
                        </ul>
                        <form method="POST">
                            <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-download me-1"></i>Install Database</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
