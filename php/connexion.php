<?php
require_once 'config.php';
if (!checkRateLimit('connexion', 10, 60)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de tentatives. Réessayez dans 1 minute."
    ]);
    exit;
}
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
if (!validateEmail($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Email ou mot de passe invalide"
    ]);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && verifyPassword($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Connexion réussie!",
            "user" => [
                "id" => $user['id'],
                "nom" => sanitize($user['nom']),
                "email" => sanitize($user['email']),
                "type" => $user['type']
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Email ou mot de passe incorrect"
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la connexion"
    ]);
}
?>
