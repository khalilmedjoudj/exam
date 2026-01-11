<?php
require_once 'config.php';
if (!checkRateLimit('inscription', 5, 60)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de tentatives. Réessayez dans 1 minute."
    ]);
    exit;
}
$nom = sanitize($_POST['nom'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? ''; 
$type = sanitize($_POST['type'] ?? 'client');
$errors = [];
if (empty($nom) || strlen($nom) < 2) {
    $errors[] = "Le nom doit contenir au moins 2 caractères";
}
if (strlen($nom) > 100) {
    $errors[] = "Le nom ne doit pas dépasser 100 caractères";
}
if (!validateEmail($email)) {
    $errors[] = "Email invalide";
}
if (strlen($password) < 6) {
    $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
}
if (!in_array($type, ['client', 'restaurant'])) {
    $errors[] = "Type de compte invalide";
}
if (!empty($errors)) {
    echo json_encode([
        "success" => false,
        "message" => implode(", ", $errors)
    ]);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Cet email est déjà utilisé"
        ]);
        exit;
    }
    $hashedPassword = hashPassword($password);
    $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $email, $hashedPassword, $type]);
    echo json_encode([
        "success" => true,
        "message" => "Inscription réussie!"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'inscription"
    ]);
}
?>
