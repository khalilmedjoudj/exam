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
