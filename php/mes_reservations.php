<?php
require_once 'config.php';

if (!checkRateLimit('mes_reservations', 30, 60)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de requêtes."
    ]);
    exit;
}

$user_id = intval($_GET['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non valide"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT r.*, a.titre, a.image_url, a.restaurant_nom, a.wilaya 
        FROM reservations r 
        JOIN annonces a ON r.annonce_id = a.id 
        WHERE r.user_id = ?
        ORDER BY r.date_reservation DESC
        LIMIT 50
    ");
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll();
    
    $reservations = array_map(function($res) {
        return [
            'id' => $res['id'],
            'annonce_id' => $res['annonce_id'],
            'titre' => sanitize($res['titre']),
            'image_url' => sanitize($res['image_url']),
            'restaurant_nom' => sanitize($res['restaurant_nom']),
            'wilaya' => sanitize($res['wilaya']),
            'statut' => $res['statut'],
            'date_reservation' => $res['date_reservation']
        ];
    }, $reservations);
    
    echo json_encode([
        "success" => true,
        "count" => count($reservations),
        "reservations" => $reservations
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des réservations"
    ]);
}
?>
