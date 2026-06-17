<?php
session_start();

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'si'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
    if (isLoggedIn()) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('UPDATE users SET language = ? WHERE id = ?');
        $stmt->execute([$_GET['lang'], $_SESSION['user_id']]);
    }
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

$translations = require __DIR__ . '/../lang/' . $_SESSION['lang'] . '.php';

function __($key): string
{
    global $translations;
    return $translations[$key] ?? $key;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isCitizen(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'citizen';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('danger', __('access_denied'));
        redirect(BASE_URL . 'auth/login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        setFlash('danger', __('access_denied'));
        redirect(BASE_URL . 'citizen/dashboard.php');
    }
}

function requireCitizen(): void
{
    requireLogin();
    if (!isCitizen()) {
        setFlash('danger', __('access_denied'));
        redirect(BASE_URL . 'admin/dashboard.php');
    }
}

function validateNIC(string $nic): bool
{
    return (bool) preg_match('/^(\d{9}[vVxX]|\d{12})$/', $nic);
}

function statusBadge(string $status): string
{
    $map = [
        'pending'    => 'warning',
        'approved'   => 'success',
        'rejected'   => 'danger',
        'completed'  => 'primary',
        'cancelled'  => 'secondary',
        'processing' => 'info',
    ];
    $class = $map[$status] ?? 'secondary';
    $label = __('status_' . $status);
    return '<span class="badge bg-' . $class . '">' . e($label) . '</span>';
}

function serviceLabel(string $key): string
{
    return __($key);
}

function createNotification(PDO $pdo, int $userId, string $message): void
{
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message) VALUES (?, ?)');
    $stmt->execute([$userId, $message]);
}

function getUnreadNotificationCount(PDO $pdo, int $userId): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

function handleUpload(array $file): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return false;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
        return false;
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $filename = uniqid('doc_', true) . '.' . $ext;
    $path = UPLOAD_DIR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        return false;
    }
    return $filename;
}

function basePath(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

function logoPath(): string
{
    return basePath(LOGO_PATH);
}

function langUrl(string $lang): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $parts = parse_url($uri);
    parse_str($parts['query'] ?? '', $query);
    $query['lang'] = $lang;
    $path = $parts['path'] ?? '';
    return $path . '?' . http_build_query($query);
}
