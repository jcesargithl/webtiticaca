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
 * Detectar la plataforma de la URL de reseña (Google, Tripadvisor o genérico)
 * @param string $url
 * @return array|null
 */
function get_review_platform_info($url) {
    $url = trim($url ?? '');
    if ($url === '') {
        return null;
    }
    
    $lower = strtolower($url);
    
    // Tripadvisor
    if (strpos($lower, 'tripadvisor') !== false) {
        $owlSvg = '<svg width="14" height="14" viewBox="0 0 713.5 713.5" style="vertical-align: -2px; margin-right: 3px;"><circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/><path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/></svg>';
        return [
            'platform' => 'tripadvisor',
            'label' => 'Tripadvisor',
            'badge_class' => 'tripadvisor-badge-pill',
            'icon_svg' => $owlSvg,
            'title' => 'Ver reseña verificada en Tripadvisor'
        ];
    }
    
    // Google Maps / Google Reviews
    if (strpos($lower, 'google') !== false || strpos($lower, 'goo.gl') !== false) {
        $googleSvg = '<svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: -1px; margin-right: 3px;"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>';
        return [
            'platform' => 'google',
            'label' => 'Google',
            'badge_class' => 'google-badge-pill',
            'icon_svg' => $googleSvg,
            'title' => 'Ver reseña en Google Maps'
        ];
    }
    
    // Genérico
    $genericSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -1px; margin-right: 3px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>';
    return [
        'platform' => 'enlace',
        'label' => 'Ver Reseña',
        'badge_class' => 'review-badge-pill',
        'icon_svg' => $genericSvg,
        'title' => 'Ver reseña original'
    ];
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

    $output = '';
    foreach ($reviews as $rev) {
        $name = htmlspecialchars($rev['name'] ?? 'Viajero');
        $role = htmlspecialchars($rev['role'] ?? 'Viajero');
        $text = htmlspecialchars($rev['text'] ?? '');
        $photo = htmlspecialchars(!empty($rev['photo']) ? $rev['photo'] : 'img/team/07.png');
        $rating = isset($rev['rating']) ? (int)$rev['rating'] : 5;
        $starsHtml = render_review_stars_html($rating);
        
        // Soporte para url de reseña (Google o Tripadvisor)
        $reviewUrl = trim(!empty($rev['review_url']) ? $rev['review_url'] : ($rev['google_url'] ?? ''));
        $platformInfo = get_review_platform_info($reviewUrl);
        $hasLink = ($platformInfo !== null);

        $output .= '                                <div class="item">' . "\n";
        
        if ($hasLink) {
            $safeUrl = htmlspecialchars($reviewUrl);
            $output .= '                                    <a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" class="testimonial-card-link" title="' . htmlspecialchars($platformInfo['title']) . '">' . "\n";
        }
        
        $output .= '                                        <p>' . $text . '</p>' . "\n";
        $output .= '                                        <div class="info">' . "\n";
        $output .= '                                            <div class="author-img"> <img src="' . $photo . '" alt="' . $name . '"> </div>' . "\n";
        $output .= '                                            <div class="cont">' . "\n";
        $output .= '                                                ' . $starsHtml . "\n";
        
        if ($hasLink) {
            $output .= '                                                <h6>' . $name . ' <span class="' . $platformInfo['badge_class'] . '" title="' . htmlspecialchars($platformInfo['title']) . '">' . $platformInfo['icon_svg'] . $platformInfo['label'] . '</span></h6> <span>' . $role . '</span>' . "\n";
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
