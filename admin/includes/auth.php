<?php
/**
 * Admin Authentication & RBAC Middleware
 * HSN SRHAN Portfolio & Strategic Platform
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/helpers.php';

function isLoggedIn() {
    return isset($_SESSION['admin_user_id']) && !empty($_SESSION['admin_user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_user_id']]);
    return $stmt->fetch();
}

function requireAuth() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
    $user = getCurrentUser();
    if (!$user || (isset($user['status']) && $user['status'] === 'suspended')) {
        session_destroy();
        header("Location: login.php?error=suspended");
        exit;
    }
}

function requireAdmin() {
    requireAuth();
    $user = getCurrentUser();
    if (!$user || $user['role'] !== 'admin') {
        die("<div style='padding:2rem; text-align:center; font-family:sans-serif;'><h2>⛔️ وصول غير مصرح</h2><p>هذه الصفحة مخصصة لمدير النظام الرئيسي فقط.</p><a href='index.php'>العودة للرئيسية</a></div>");
    }
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
