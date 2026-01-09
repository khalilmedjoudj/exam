<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "barakafood";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(["success" => false, "message" => "Erreur de connexion à la base de données"]));
}
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
function checkRateLimit($action, $limit = 10, $timeWindow = 60) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = md5($ip . $action);
    $file = sys_get_temp_dir() . "/rate_limit_$key.txt";
    $now = time();
    $requests = [];
    if (file_exists($file)) {
        $data = file_get_contents($file);
        $requests = json_decode($data, true) ?: [];
    }
    $requests = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });
    if (count($requests) >= $limit) {
        return false; 
    }
    $requests[] = $now;
    file_put_contents($file, json_encode($requests));
    return true; 
}
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function sendSecurityHeaders() {
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'self' https://unpkg.com https://code.jquery.com https://images.unsplash.com; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com;");
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
}
sendSecurityHeaders();
?>
