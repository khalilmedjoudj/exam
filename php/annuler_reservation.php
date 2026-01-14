<?php
require_once 'config.php';

if (!checkRateLimit('annuler', 20, 3600)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de requêtes. Réessayez plus tard."
    ]);
    exit;
}

$user_id = intval($_POST['user_id'] ?? 0);
$reservation_id = intval($_POST['reservation_id'] ?? 0);

if ($user_id <= 0 || $reservation_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Données invalides"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT r.*, a.id as annonce_id FROM reservations r JOIN annonces a ON r.annonce_id = a.id WHERE r.id = ? AND r.user_id = ?");
    $stmt->execute([$reservation_id, $user_id]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        echo json_encode([
            "success" => false,
            "message" => "Réservation non trouvée ou vous n'êtes pas autorisé"
        ]);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);
    
    $stmt = $pdo->prepare("UPDATE annonces SET statut = 'disponible' WHERE id = ?");
    $stmt->execute([$reservation['annonce_id']]);
    
    echo json_encode([
        "success" => true,
        "message" => "Commande annulée avec succès!"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'annulation"
    ]);
}
?>
