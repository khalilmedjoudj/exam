<?php
require_once 'config.php';
if (!checkRateLimit('get_annonces', 60, 60)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de requêtes. Réessayez plus tard."
    ]);
    exit;
}
$recherche = sanitize($_GET['recherche'] ?? '');
$wilaya = sanitize($_GET['wilaya'] ?? '');
try {
    $sql = "SELECT * FROM annonces WHERE statut = 'disponible'";
    $params = [];
    if (!empty($recherche)) {
        $sql .= " AND (titre LIKE ? OR description LIKE ?)";
        $params[] = "%$recherche%";
        $params[] = "%$recherche%";
    }
    if (!empty($wilaya)) {
        $sql .= " AND wilaya LIKE ?";
        $params[] = "%$wilaya%";
    }
    $sql .= " ORDER BY date_creation DESC LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $annonces = $stmt->fetchAll();
    $annonces = array_map(function($annonce) {
        return [
            'id' => $annonce['id'],
            'titre' => sanitize($annonce['titre']),
            'description' => sanitize($annonce['description']),
            'categorie' => sanitize($annonce['categorie']),
            'image_url' => sanitize($annonce['image_url']),
            'restaurant_nom' => sanitize($annonce['restaurant_nom']),
            'wilaya' => sanitize($annonce['wilaya']),
            'adresse' => sanitize($annonce['adresse']),
            'statut' => $annonce['statut'],
            'user_id' => $annonce['user_id'],
            'date_creation' => $annonce['date_creation']
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
