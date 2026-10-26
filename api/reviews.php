<?php
// api/reviews.php - Endpoint JSON para obtener reseñas aleatorias activas
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once __DIR__ . '/../includes/reviews_helpers.php';

$limit = isset($_GET['limit']) ? max(1, min(20, (int)$_GET['limit'])) : 3;
$reviews = get_random_reviews($limit);

echo json_encode([
    'success' => true,
    'total' => count($reviews),
    'reviews' => $reviews
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
