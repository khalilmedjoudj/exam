<?php
require_once 'config.php';
if (!checkRateLimit('changer_statut', 30, 60)) {
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "message" => "Trop de requêtes."
    ]);
    exit;
