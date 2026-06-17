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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">DSSMS Minipe – Database Setup</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                        <a href="index.php" class="btn btn-primary">Go to Home</a>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <p>Click below to create the database, tables, and default accounts.</p>
                        <ul>
                            <li>Ensure MySQL is running (XAMPP/WAMP/Laragon)</li>
                            <li>Update credentials in <code>config/database.php</code> if needed</li>
                        </ul>
                        <form method="POST">
                            <button type="submit" class="btn btn-primary">Install Database</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
