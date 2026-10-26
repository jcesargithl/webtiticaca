<?php
// includes/reviews_helpers.php
// Funciones de gestión y renderizado de reseñas / testimonios

define('REVIEWS_FILE', __DIR__ . '/../data/reviews.json');

/**
 * Obtener todas las reseñas
 * @param bool $only_active Si es true, solo devuelve las activas
 * @return array
 */
function get_all_reviews($only_active = false) {
    if (!file_exists(REVIEWS_FILE)) {
        return [];
    }
    $json = file_get_contents(REVIEWS_FILE);
    $reviews = json_decode($json, true);
    if (!is_array($reviews)) {
        return [];
    }
    if ($only_active) {
        $reviews = array_values(array_filter($reviews, function($r) {
            return !isset($r['active']) || $r['active'] === true;
        }));
    }
    return $reviews;
}

/**
 * Guardar la lista de reseñas en JSON
 * @param array $reviews
 * @return bool
 */
function save_reviews($reviews) {
    $dir = dirname(REVIEWS_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $json = json_encode(array_values($reviews), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents(REVIEWS_FILE, $json) !== false;
}

/**
 * Obtener una reseña por su ID
 * @param string $id
 * @return array|null
 */
function get_review_by_id($id) {
    $reviews = get_all_reviews(false);
    foreach ($reviews as $r) {
        if (isset($r['id']) && $r['id'] === $id) {
            return $r;
        }
    }
    return null;
}

/**
 * Obtener N reseñas al azar de las activas
 * @param int $limit Número de reseñas (por defecto 3)
 * @return array
 */
function get_random_reviews($limit = 3) {
    $active = get_all_reviews(true);
    if (empty($active)) {
        return [];
    }
    
    // Mezclar aleatoriamente en cada recarga
    shuffle($active);
    
    if (count($active) > $limit) {
        return array_slice($active, 0, $limit);
    }
    return $active;
}

/**
 * Generar HTML de las estrellas de calificación
 * @param int $rating 1 a 5
 * @return string
 */
function render_review_stars_html($rating = 5) {
    $rating = max(1, min(5, (int)$rating));
    $html = '<div class="rating">';
    for ($i = 1; $i <= 5; $i++) {
        $activeClass = ($i <= $rating) ? 'active' : '';
        $html .= ' <i class="star ' . $activeClass . '"></i>';
    }
    $html .= ' </div>';
    return $html;
}

/**
 * Renderizar los items del carrusel de testimonios
 * @param array|null $reviews Lista de reseñas a mostrar (si es null, saca 3 al azar)
 * @return string
 */
function render_testimonials_carousel_items($reviews = null) {
    if ($reviews === null) {
        $reviews = get_random_reviews(3);
    }
    
    if (empty($reviews)) {
        return '';
    }
    
    $googleIconSvg = '<svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: -1px; margin-right: 3px;"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>';

    $output = '';
    foreach ($reviews as $rev) {
        $name = htmlspecialchars($rev['name'] ?? 'Viajero');
        $role = htmlspecialchars($rev['role'] ?? 'Viajero');
        $text = htmlspecialchars($rev['text'] ?? '');
        $photo = htmlspecialchars(!empty($rev['photo']) ? $rev['photo'] : 'img/team/07.png');
        $rating = isset($rev['rating']) ? (int)$rev['rating'] : 5;
        $starsHtml = render_review_stars_html($rating);
        $googleUrl = trim($rev['google_url'] ?? '');
        $hasLink = !empty($googleUrl);

        $output .= '                                <div class="item">' . "\n";
        
        if ($hasLink) {
            $safeUrl = htmlspecialchars($googleUrl);
            $output .= '                                    <a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" class="testimonial-card-link" title="Ver reseña en Google">' . "\n";
        }
        
        $output .= '                                        <p>' . $text . '</p>' . "\n";
        $output .= '                                        <div class="info">' . "\n";
        $output .= '                                            <div class="author-img"> <img src="' . $photo . '" alt="' . $name . '"> </div>' . "\n";
        $output .= '                                            <div class="cont">' . "\n";
        $output .= '                                                ' . $starsHtml . "\n";
        
        if ($hasLink) {
            $output .= '                                                <h6>' . $name . ' <span class="google-badge-pill" title="Reseña en Google">' . $googleIconSvg . 'Google</span></h6> <span>' . $role . '</span>' . "\n";
        } else {
            $output .= '                                                <h6>' . $name . '</h6> <span>' . $role . '</span>' . "\n";
        }
        
        $output .= '                                            </div>' . "\n";
        $output .= '                                        </div>' . "\n";
        
        if ($hasLink) {
            $output .= '                                    </a>' . "\n";
        }
        
        $output .= '                                </div>' . "\n";
    }
    
    return $output;
}
