<?php
require_once 'config.php';
if (!checkRateLimit('reserver', 20, 3600)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de réservations. Réessayez plus tard."
    ]);
    exit;
}
$user_id = intval($_POST['user_id'] ?? 0);
$annonce_id = intval($_POST['annonce_id'] ?? 0);
if ($user_id <= 0 || $annonce_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Données invalides"
    ]);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT type FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user || $user['type'] !== 'client') {
        echo json_encode([
            "success" => false,
            "message" => "Seuls les clients peuvent réserver des plats"
        ]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = ? AND statut = 'disponible'");
    $stmt->execute([$annonce_id]);
    if (!$stmt->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Cette annonce n'est plus disponible"
        ]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND annonce_id = ?");
    $stmt->execute([$user_id, $annonce_id]);
    if ($stmt->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Vous avez déjà réservé ce plat"
        ]);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, annonce_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $annonce_id]);
    $stmt = $pdo->prepare("UPDATE annonces SET statut = 'reserve' WHERE id = ?");
    $stmt->execute([$annonce_id]);
    echo json_encode([
        "success" => true,
        "message" => "Réservation effectuée avec succès!"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la réservation"
    ]);
}
?>
