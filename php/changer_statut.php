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
if (!in_array($nouveau_statut, ['disponible', 'indisponible'])) {
    echo json_encode([
        "success" => false,
        "message" => "Statut invalide"
    ]);
    exit;
