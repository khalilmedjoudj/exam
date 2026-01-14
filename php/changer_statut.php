<?php
require_once 'config.php';
if (!checkRateLimit('changer_statut', 30, 60)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de requêtes."
    ]);
    exit;
}
$annonce_id = intval($_POST['annonce_id'] ?? 0);
$nouveau_statut = sanitize($_POST['statut'] ?? '');
$user_id = intval($_POST['user_id'] ?? 0);
if ($annonce_id <= 0 || $user_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Données invalides"
    ]);
    exit;
}
if (!in_array($nouveau_statut, ['disponible', 'reserve', 'termine'])) {
    echo json_encode([
        "success" => false,
        "message" => "Statut invalide"
    ]);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT user_id FROM annonces WHERE id = ?");
    $stmt->execute([$annonce_id]);
    $annonce = $stmt->fetch();
    if (!$annonce || $annonce['user_id'] != $user_id) {
        echo json_encode([
            "success" => false,
            "message" => "Cette annonce ne vous appartient pas"
        ]);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE annonces SET statut = ? WHERE id = ?");
    $stmt->execute([$nouveau_statut, $annonce_id]);
    echo json_encode([
        "success" => true,
        "message" => "Statut mis à jour avec succès"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la mise à jour"
    ]);
}
?>
