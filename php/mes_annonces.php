<?php
require_once 'config.php';
if (!checkRateLimit('mes_annonces', 30, 60)) {
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
    $stmt = $pdo->prepare("SELECT type FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user || $user['type'] !== 'restaurant') {
        echo json_encode([
            "success" => false,
            "message" => "Accès réservé aux restaurants"
        ]);
        exit;
    }
    $stmt = $pdo->prepare("
        SELECT a.*, 
               (SELECT COUNT(*) FROM reservations r WHERE r.annonce_id = a.id) as nb_reservations
        FROM annonces a 
        WHERE a.user_id = ?
        ORDER BY a.date_creation DESC
        LIMIT 50
    ");
    $stmt->execute([$user_id]);
    $annonces = $stmt->fetchAll();
    $annonces = array_map(function($a) {
        return [
            'id' => $a['id'],
            'titre' => sanitize($a['titre']),
            'description' => sanitize($a['description']),
            'categorie' => sanitize($a['categorie']),
            'image_url' => sanitize($a['image_url']),
            'wilaya' => sanitize($a['wilaya']),
            'adresse' => sanitize($a['adresse']),
            'statut' => $a['statut'],
            'nb_reservations' => $a['nb_reservations'],
            'date_creation' => $a['date_creation']
        ];
    }, $annonces);
    echo json_encode([
        "success" => true,
        "count" => count($annonces),
        "annonces" => $annonces
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des annonces"
    ]);
}
?>
