<?php
require_once __DIR__ . "/config.php";

function e($v) {
    return htmlspecialchars((string)($v ?? ""), ENT_QUOTES, "UTF-8");
}

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function currentUser() {
    global $conn;

    if (!isLoggedIn()) return null;

    $id = (int)$_SESSION["user_id"];

    $st = $conn->prepare(
        "SELECT * FROM users WHERE id=? LIMIT 1"
    );

    $st->bind_param("i", $id);
    $st->execute();

    return $st->get_result()->fetch_assoc();
}

function requireAdmin() {
    requireLogin();

    $u = currentUser();

    if (!$u || (int)$u["is_admin"] !== 1) {
        http_response_code(403);
        die("Access denied.");
    }
}

function csrfToken() {
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf"];
}

function verifyCsrf($token) {
    return isset($_SESSION["csrf"]) &&
           hash_equals($_SESSION["csrf"], (string)$token);
}

function getSetting($key) {
    global $conn;

    $st = $conn->prepare(
        "SELECT setting_value FROM settings
         WHERE setting_key=? LIMIT 1"
    );

    $st->bind_param("s", $key);
    $st->execute();

    $r = $st->get_result()->fetch_assoc();

    return $r ? $r["setting_value"] : "";
}

function setSetting($key, $value) {
    global $conn;

    $st = $conn->prepare(
        "INSERT INTO settings(setting_key,setting_value)
         VALUES(?,?)
         ON DUPLICATE KEY UPDATE
         setting_value=VALUES(setting_value)"
    );

    $st->bind_param("ss", $key, $value);

    return $st->execute();
}

function go($url) {
    header("Location: " . $url);
    exit;
}
?>