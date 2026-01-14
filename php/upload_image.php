<?php
require_once 'config.php';
if (!checkRateLimit('upload', 10, 3600)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop d'uploads. Réessayez plus tard."
    ]);
    exit;
}
$uploadDir = '../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => "Fichier trop volumineux (limite serveur)",
        UPLOAD_ERR_FORM_SIZE => "Fichier trop volumineux",
        UPLOAD_ERR_PARTIAL => "Upload incomplet",
        UPLOAD_ERR_NO_FILE => "Aucun fichier reçu",
        UPLOAD_ERR_NO_TMP_DIR => "Erreur serveur",
        UPLOAD_ERR_CANT_WRITE => "Erreur d'écriture",
        UPLOAD_ERR_EXTENSION => "Extension bloquée"
    ];
    $error = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
    echo json_encode([
        "success" => false,
        "message" => $errorMessages[$error] ?? "Erreur inconnue"
    ]);
    exit;
}
$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($fileTmpName);
$allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];
if (!array_key_exists($mimeType, $allowedMimes)) {
    echo json_encode([
        "success" => false,
        "message" => "Type de fichier non autorisé. Utilisez: JPG, PNG, GIF, WEBP"
    ]);
    exit;
}
$maxSize = 5 * 1024 * 1024;
if ($fileSize > $maxSize) {
    echo json_encode([
        "success" => false,
        "message" => "Le fichier est trop volumineux (max 5MB)"
    ]);
    exit;
}
$imageInfo = getimagesize($fileTmpName);
if ($imageInfo === false) {
    echo json_encode([
        "success" => false,
        "message" => "Fichier image invalide"
    ]);
    exit;
}
if ($imageInfo[0] > 4000 || $imageInfo[1] > 4000) {
    echo json_encode([
        "success" => false,
        "message" => "Image trop grande (max 4000x4000 pixels)"
    ]);
    exit;
}
$extension = $allowedMimes[$mimeType];
$newFileName = bin2hex(random_bytes(16)) . '.' . $extension;
$destination = $uploadDir . $newFileName;
if (move_uploaded_file($fileTmpName, $destination)) {
    echo json_encode([
        "success" => true,
        "message" => "Image uploadée avec succès",
        "image_url" => "uploads/" . $newFileName
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'upload"
    ]);
}
?>
