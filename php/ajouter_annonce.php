  <?php
require_once 'config.php';
if (!checkRateLimit('ajouter_annonce', 10, 3600)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Limite d'annonces atteinte. Réessayez plus tard."
    ]);
    exit;
}
$titre = sanitize($_POST['titre'] ?? '');
$description = sanitize($_POST['description'] ?? '');
$categorie = sanitize($_POST['categorie'] ?? '');
$image_url = sanitize($_POST['image_url'] ?? '');
$restaurant_nom = sanitize($_POST['restaurant_nom'] ?? '');
$wilaya = sanitize($_POST['wilaya'] ?? '');
$adresse = sanitize($_POST['adresse'] ?? '');
$user_id = intval($_POST['user_id'] ?? 0);
$errors = [];
if (empty($titre) || strlen($titre) < 3) {
    $errors[] = "Le titre doit contenir au moins 3 caractères";
}
if (strlen($titre) > 200) {
    $errors[] = "Le titre ne doit pas dépasser 200 caractères";
}
if (empty($description) || strlen($description) < 10) {
    $errors[] = "La description doit contenir au moins 10 caractères";
}
if (empty($categorie)) {
    $errors[] = "La catégorie est obligatoire";
}
if (empty($wilaya)) {
    $errors[] = "La wilaya est obligatoire";
}
if (empty($adresse)) {
    $errors[] = "L'adresse est obligatoire";
}
if ($user_id <= 0) {
    $errors[] = "Utilisateur non valide";
}
if (!empty($image_url) && !filter_var($image_url, FILTER_VALIDATE_URL)) {
    if (!preg_match('/^uploads\/[\w\-\.]+\.(jpg|jpeg|png|gif|webp)$/i', $image_url)) {
        $image_url = "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500";
    }
}
if (!empty($errors)) {
    echo json_encode([
        "success" => false,
        "message" => implode(", ", $errors)
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
            "message" => "Seuls les restaurants peuvent publier des annonces"
        ]);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO annonces (titre, description, categorie, image_url, restaurant_nom, wilaya, adresse, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $description, $categorie, $image_url, $restaurant_nom, $wilaya, $adresse, $user_id]);
    echo json_encode([
        "success" => true,
        "message" => "Annonce ajoutée avec succès!",
        "id" => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'ajout de l'annonce "
    ]);
}
?>

