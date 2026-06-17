<?php
require_once __DIR__ . '/functions.php';

function loginUser(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['lang'] = $user['language'] ?? 'en';
}

function logoutUser(): void
{
    session_unset();
    session_destroy();
}

function getCurrentUser(PDO $pdo): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}
