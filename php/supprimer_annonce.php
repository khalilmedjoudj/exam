<?php
require_once 'config.php';
if (!checkRateLimit('supprimer_annonce', 20, 3600)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de suppressions. Réessayez plus tard."
    ]);
    exit;
}
$annonce_id = intval($_POST['annonce_id'] ?? 0);
$user_id = intval($_POST['user_id'] ?? 0);
if ($annonce_id <= 0 || $user_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Données invalides"
    ]);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT user_id, image_url FROM annonces WHERE id = ?");
    $stmt->execute([$annonce_id]);
    $annonce = $stmt->fetch();
    if (!$annonce || $annonce['user_id'] != $user_id) {
        echo json_encode([
            "success" => false,
            "message" => "Cette annonce ne vous appartient pas"
        ]);
        exit;
    }
    if ($annonce['image_url'] && strpos($annonce['image_url'], 'uploads/') === 0) {
        $imagePath = '../' . $annonce['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    $stmt = $pdo->prepare("DELETE FROM annonces WHERE id = ?");
    $stmt->execute([$annonce_id]);
    echo json_encode([
        "success" => true,
        "message" => "Annonce supprimée avec succès"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la suppression"
    ]);
}
?>
