<?php
// resenas.php - Página Oficial de Reseñas y Experiencias de Viajeros
require_once __DIR__ . '/includes/reviews_helpers.php';

$reviews = get_all_reviews(true);
$stats = get_reviews_stats();

// Enlaces por defecto a perfiles si no vienen en reseñas individuales
$google_profile_url = !empty($stats['google_url']) 
    ? $stats['google_url'] 
    : 'https://www.google.com/maps/search/?api=1&query=Titicaca+Experiences+Puno';

$tripadvisor_profile_url = !empty($stats['tripadvisor_url']) 
    ? $stats['tripadvisor_url'] 
    : 'https://www.tripadvisor.com.pe/Search?q=Titicaca+Experiences';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Reseñas y Opiniones de Nuestros Viajeros - Titicaca Experiences Perú</title>
    <meta name="description" content="Conoce las opiniones y testimonios de quienes viajaron con Titicaca Experiences en el Lago Titicaca, Puno, Cusco y Bolivia. Calificaciones verificadas en Google Maps y Tripadvisor.">
    <link rel="shortcut icon" href="img/favicon.png" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600&amp;family=Poppins:wght@300;400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="css/plugins.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/translator.css" />
    
    <!-- Schema.org Rich Snippets para Google -->
    <?= render_reviews_schema_json($reviews, $stats); ?>

    <style>
        /* ==============================================================
           ESTILOS EXCLUSIVOS Y ELEGANTES PARA LA PÁGINA DE RESEÑAS
        ============================================================== */
        :root {
            --brand-navy: #0f2454;
            --brand-gold: #c5a880;
            --brand-gold-dark: #b39368;
            --star-gold: #f59e0b;
        }

        /* Banner Header */
        .banner-header.reviews-banner {
            background-position: center center;
            background-size: cover;
            min-height: 420px;
            position: relative;
        }

        /* Scoreboard / Panel de Reputación Multifuente */
        .reviews-scoreboard-wrapper {
            margin-top: -65px;
            position: relative;
            z-index: 20;
            margin-bottom: 50px;
        }
        .reviews-scoreboard {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(15, 36, 84, 0.08);
            border: 1px solid rgba(15, 36, 84, 0.06);
            padding: 35px 30px;
            transition: transform 0.3s ease;
        }
        .scoreboard-score-number {
            font-size: 52px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            color: var(--brand-navy);
            line-height: 1;
        }
        .scoreboard-stars {
            color: var(--star-gold);
            font-size: 20px;
            letter-spacing: 2px;
        }
        .scoreboard-divider {
            border-right: 1px solid #edf2f7;
        }
        @media (max-width: 991px) {
            .scoreboard-divider {
                border-right: none;
                border-bottom: 1px solid #edf2f7;
                padding-bottom: 25px;
                margin-bottom: 25px;
            }
        }

        /* Cajas de Plataformas en el Scoreboard */
        .platform-channel-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 18px;
            height: 100%;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .platform-channel-card:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        /* Botón de acción para calificar */
        .btn-leave-review-cta {
            background: var(--brand-navy);
            color: #ffffff !important;
            border-radius: 8px;
            padding: 12px 22px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(15, 36, 84, 0.2);
            text-decoration: none;
        }
        .btn-leave-review-cta:hover {
            background: var(--brand-gold);
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(197, 168, 128, 0.4);
        }

        /* Pestañas de Filtro por Plataforma */
        .reviews-filter-nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 45px;
        }
        .reviews-filter-btn {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .reviews-filter-btn:hover {
            border-color: var(--brand-navy);
            color: var(--brand-navy);
        }
        .reviews-filter-btn.active {
            background: var(--brand-navy);
            border-color: var(--brand-navy);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(15, 36, 84, 0.25);
        }
        .reviews-filter-btn .filter-count-badge {
            background: rgba(0,0,0,0.08);
            color: inherit;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
            line-height: 1.2;
        }
        .reviews-filter-btn.active .filter-count-badge {
            background: rgba(255,255,255,0.25);
            color: #ffffff;
        }

        /* Tarjeta de Reseña (Review Card) */
        .review-card-item {
            transition: opacity 0.35s ease, transform 0.35s ease;
        }
        .review-card-item.is-hidden {
            display: none !important;
        }
        .review-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(15, 36, 84, 0.08);
            box-shadow: 0 8px 24px rgba(15, 36, 84, 0.04);
            padding: 28px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s ease;
        }
        .review-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(15, 36, 84, 0.1);
            border-color: rgba(197, 168, 128, 0.5);
        }
        .review-card-quote-icon {
            position: absolute;
            top: 24px;
            right: 28px;
            font-size: 28px;
            color: rgba(197, 168, 128, 0.25);
            line-height: 1;
        }
        .review-author-thumb {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--brand-gold);
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        }
        .review-author-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: var(--brand-navy);
            margin-bottom: 2px;
            line-height: 1.2;
        }
        .review-author-role {
            font-size: 12px;
            color: #64748b;
            font-weight: 400;
        }
        .review-card-stars {
            color: var(--star-gold);
            font-size: 14px;
            margin: 14px 0 10px 0;
            letter-spacing: 2px;
        }
        .review-card-text {
            font-size: 14px;
            color: #334155;
            line-height: 1.7;
            font-family: 'Barlow', sans-serif;
            font-weight: 400;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .review-card-footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 14px;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Insignias de Verificación */
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .verified-badge-google {
            background: #e8f0fe;
            color: #1a73e8;
            border: 1px solid #d2e3fc;
        }
        .verified-badge-google:hover {
            background: #1a73e8;
            color: #ffffff;
        }
        .verified-badge-tripadvisor {
            background: #eefaf4;
            color: #004f32;
            border: 1px solid #b7edd8;
        }
        .verified-badge-tripadvisor:hover {
            background: #00aa6c;
            color: #ffffff;
        }
        .verified-badge-web {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Sección de Invitación a Calificar / CTA */
        .leave-review-section {
            background: linear-gradient(135deg, #091736 0%, #0f2454 100%);
            border-radius: 20px;
            padding: 50px 40px;
            color: #ffffff;
            margin-top: 60px;
            position: relative;
            overflow: hidden;
        }
        .leave-review-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(197, 168, 128, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader-bg"></div>
    <div id="preloader">
        <div id="preloader-status">
            <div class="preloader-position loader"> <span></span> </div>
        </div>
    </div>

    <!-- Progress scroll totop -->
    <div class="progress-wrap cursor-pointer">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    <!-- Navbar Principal -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- Header Banner -->
    <div class="banner-header section-padding valign bg-img bg-fixed reviews-banner" data-overlay-dark="6" data-background="img/slider/home-review.jpg">
        <div class="container">
            <div class="row">
                <div class="col-md-12 caption mt-90 text-center">
                    <h5>Experiencias Inolvidables</h5>
                    <h1><span>Reseñas de </span> Nuestros Viajeros</h1>
                    <p style="color: #e2e8f0; max-width: 650px; margin: 12px auto 0; font-size: 15px;">
                        Descubre las vivencias, opiniones y recuerdos de quienes confiaron en nosotros para explorar el Lago Titicaca, los Andes y el Perú.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Principal -->
    <section class="section-padding pt-0" style="background-color: #f8fafc; padding-bottom: 90px;">
        <div class="container">
            
            <!-- 1. Scoreboard / Panel de Reputación Multifuente -->
            <div class="reviews-scoreboard-wrapper">
                <div class="reviews-scoreboard">
                    <div class="row align-items-center">
                        
                        <!-- Columna 1: Puntuación General -->
                        <div class="col-lg-4 col-md-5 scoreboard-divider text-center text-md-start">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                                <div class="scoreboard-score-number"><?= htmlspecialchars($stats['average']) ?></div>
                                <div>
                                    <div class="scoreboard-stars">
                                        <i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i>
                                    </div>
                                    <div style="font-size: 13px; color: #64748b; font-weight: 500; margin-top: 3px;">
                                        Basado en <?= $stats['total'] ?> opiniones verificadas
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-muted" style="font-size: 12px;">
                                <span class="badge bg-success" style="font-size: 11px; font-weight: 600; padding: 4px 8px;">100% Recomendado</span> Satisfacción garantizada
                            </div>
                        </div>

                        <!-- Columna 2: Desglose por Canales (Google & Tripadvisor) -->
                        <div class="col-lg-5 col-md-7 mb-3 mb-lg-0">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="platform-channel-card">
                                        <div class="d-flex align-items-center gap-2">
                                            <svg width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/>
                                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                                <path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/>
                                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                                            </svg>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">Google Maps</div>
                                                <div style="font-size: 11px; color: #64748b;"><?= $stats['google_count'] ?> reseña<?= $stats['google_count'] == 1 ? '' : 's' ?></div>
                                            </div>
                                        </div>
                                        <a href="<?= htmlspecialchars($google_profile_url) ?>" target="_blank" rel="noopener noreferrer" class="text-primary small" title="Ver en Google Maps">
                                            <i class="ti-arrow-top-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="platform-channel-card">
                                        <div class="d-flex align-items-center gap-2">
                                            <svg width="20" height="20" viewBox="0 0 713.5 713.5">
                                                <circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/>
                                                <path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/>
                                            </svg>
                                            <div>
                                                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">Tripadvisor</div>
                                                <div style="font-size: 11px; color: #64748b;"><?= $stats['tripadvisor_count'] ?> opinión<?= $stats['tripadvisor_count'] == 1 ? '' : 'es' ?></div>
                                            </div>
                                        </div>
                                        <a href="<?= htmlspecialchars($tripadvisor_profile_url) ?>" target="_blank" rel="noopener noreferrer" class="text-success small" title="Ver en Tripadvisor">
                                            <i class="ti-arrow-top-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna 3: Botón de Llamado a la Acción -->
                        <div class="col-lg-3 text-center text-lg-end">
                            <a href="<?= htmlspecialchars($google_profile_url) ?>" target="_blank" rel="noopener noreferrer" class="btn-leave-review-cta w-100 justify-content-center">
                                <i class="ti-pencil-alt"></i> Escribir una Reseña
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 2. Barra de Filtros Interactivos (Tabs) -->
            <div class="reviews-filter-nav">
                <button type="button" class="reviews-filter-btn active" data-filter="all">
                    <span>Todas las Reseñas</span>
                    <span class="filter-count-badge"><?= $stats['total'] ?></span>
                </button>
                <button type="button" class="reviews-filter-btn" data-filter="google">
                    <svg width="13" height="13" viewBox="0 0 24 24" style="vertical-align: -1px;">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Google Maps</span>
                    <span class="filter-count-badge"><?= $stats['google_count'] ?></span>
                </button>
                <button type="button" class="reviews-filter-btn" data-filter="tripadvisor">
                    <svg width="14" height="14" viewBox="0 0 713.5 713.5" style="vertical-align: -2px;">
                        <circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/>
                        <path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/>
                    </svg>
                    <span>Tripadvisor</span>
                    <span class="filter-count-badge"><?= $stats['tripadvisor_count'] ?></span>
                </button>
                <button type="button" class="reviews-filter-btn" data-filter="web">
                    <i class="ti-check-box" style="font-size: 13px;"></i>
                    <span>Web Directa</span>
                    <span class="filter-count-badge"><?= $stats['web_count'] ?></span>
                </button>
            </div>

            <!-- 3. Grid de Tarjetas de Reseña -->
            <div class="row" id="reviews-grid-container">
                <?php if (empty($reviews)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-info">Aún no hay reseñas activas disponibles. ¡Sé el primero en compartir tu experiencia!</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): 
                        $source = get_review_source_type($rev);
                        $name = htmlspecialchars($rev['name'] ?? 'Viajero');
                        $role = htmlspecialchars($rev['role'] ?? 'Viajero');
                        $text = htmlspecialchars($rev['text'] ?? '');
                        $photo = htmlspecialchars(!empty($rev['photo']) ? $rev['photo'] : 'img/team/07.png');
                        $rating = isset($rev['rating']) ? (int)$rev['rating'] : 5;
                        $reviewUrl = trim(!empty($rev['review_url']) ? $rev['review_url'] : ($rev['google_url'] ?? ''));
                        $platformInfo = get_review_platform_info($reviewUrl);
                    ?>
                        <div class="col-lg-4 col-md-6 mb-30 review-card-item" data-source="<?= $source ?>">
                            <div class="review-card">
                                
                                <div class="review-card-quote-icon">
                                    <i class="ti-quote-left"></i>
                                </div>

                                <!-- Autor -->
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $photo ?>" alt="<?= $name ?>" class="review-author-thumb" onerror="this.src='img/team/07.png'">
                                    <div>
                                        <h4 class="review-author-name"><?= $name ?></h4>
                                        <div class="review-author-role"><?= $role ?></div>
                                    </div>
                                </div>

                                <!-- Estrellas de Calificación -->
                                <div class="review-card-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="star<?= $i <= $rating ? ' active' : '' ?>" style="color: <?= $i <= $rating ? '#f59e0b' : '#cbd5e1' ?>;">★</i>
                                    <?php endfor; ?>
                                </div>

                                <!-- Contenido / Texto de la Reseña -->
                                <div class="review-card-text">
                                    "<?= $text ?>"
                                </div>

                                <!-- Pie de Tarjeta con Badge de Verificación -->
                                <div class="review-card-footer">
                                    <?php if ($source === 'google' && !empty($reviewUrl)): ?>
                                        <a href="<?= htmlspecialchars($reviewUrl) ?>" target="_blank" rel="noopener noreferrer" class="verified-badge verified-badge-google" title="Ver reseña original en Google Maps">
                                            <svg width="12" height="12" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/>
                                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                                <path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/>
                                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                                            </svg>
                                            <span>Google Maps</span>
                                            <i class="ti-arrow-top-right ms-1"></i>
                                        </a>
                                        <span class="text-muted" style="font-size: 11px;">Verificada</span>

                                    <?php elseif ($source === 'tripadvisor' && !empty($reviewUrl)): ?>
                                        <a href="<?= htmlspecialchars($reviewUrl) ?>" target="_blank" rel="noopener noreferrer" class="verified-badge verified-badge-tripadvisor" title="Ver reseña original en Tripadvisor">
                                            <svg width="13" height="13" viewBox="0 0 713.5 713.5">
                                                <circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/>
                                                <path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/>
                                            </svg>
                                            <span>Tripadvisor</span>
                                            <i class="ti-arrow-top-right ms-1"></i>
                                        </a>
                                        <span class="text-muted" style="font-size: 11px;">Verificada</span>

                                    <?php else: ?>
                                        <span class="verified-badge verified-badge-web">
                                            <i class="ti-check" style="color: #10b981;"></i>
                                            <span>Opinión de Pasajero</span>
                                        </span>
                                        <span class="text-muted" style="font-size: 11px;">Titicaca Exp.</span>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Mensaje cuando un filtro no tiene resultados -->
            <div id="no-reviews-msg" class="text-center py-5 d-none">
                <div class="p-4 bg-white rounded-3 shadow-sm border d-inline-block">
                    <i class="ti-info-alt text-primary mb-2" style="font-size: 28px;"></i>
                    <h5 class="mb-1" style="color: #0f2454;">No se encontraron reseñas en esta categoría</h5>
                    <p class="text-muted small mb-0">Selecciona "Todas las Reseñas" para ver el catálogo completo.</p>
                </div>
            </div>

            <!-- 4. Sección de Invitación a Calificar (Call To Action) -->
            <div class="leave-review-section">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <span style="color: var(--brand-gold); font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px;">Tu opinión nos inspira</span>
                        <h2 style="color: #ffffff; font-size: 32px; font-weight: 700; margin: 8px 0 12px 0; font-family: 'Poppins', sans-serif;">¿Viajaste recientemente con nosotros?</h2>
                        <p style="color: #cbd5e1; font-size: 15px; margin-bottom: 0; max-width: 620px; line-height: 1.6;">
                            Ayuda a otros viajeros de todo el mundo a descubrir la magia del Lago Titicaca y los Andes peruanos compartiendo tu experiencia sincera.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                            <a href="<?= htmlspecialchars($google_profile_url) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light" style="font-weight: 600; border-radius: 8px; font-size: 13px; padding: 12px 18px;">
                                <i class="ti-google me-1" style="color: #4285F4;"></i> Calificar en Google Maps
                            </a>
                            <a href="https://wa.me/51950766721?text=Hola,%20deseo%20compartir%20mi%20rese%C3%B1a%20sobre%20el%20tour" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light" style="font-weight: 600; border-radius: 8px; font-size: 13px; padding: 12px 18px;">
                                <i class="ti-comment-alt me-1"></i> Enviar testimonio por WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer Modular -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- jQuery & Plugins -->
    <script src="js/jquery-3.6.3.min.js"></script>
    <script src="js/jquery-migrate-3.0.0.min.js"></script>
    <script src="js/modernizr-2.6.2.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/pace.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scrollIt.min.js"></script>
    <script src="js/custom.js"></script>

    <!-- Script de Filtro Interactivo de Reseñas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var filterButtons = document.querySelectorAll('.reviews-filter-btn');
            var reviewItems = document.querySelectorAll('.review-card-item');
            var noReviewsMsg = document.getElementById('no-reviews-msg');

            filterButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    // Remover estado activo de los demás botones
                    filterButtons.forEach(function(b) { b.classList.remove('active'); });
                    this.classList.add('active');

                    var filterValue = this.getAttribute('data-filter');
                    var visibleCount = 0;

                    reviewItems.forEach(function(item) {
                        var itemSource = item.getAttribute('data-source');
                        
                        if (filterValue === 'all' || itemSource === filterValue) {
                            item.classList.remove('is-hidden');
                            item.style.opacity = '0';
                            item.style.transform = 'translateY(10px)';
                            
                            setTimeout(function() {
                                item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                item.style.opacity = '1';
                                item.style.transform = 'translateY(0)';
                            }, 30);
                            visibleCount++;
                        } else {
                            item.classList.add('is-hidden');
                        }
                    });

                    // Mostrar o esconder mensaje de no resultados
                    if (visibleCount === 0) {
                        noReviewsMsg.classList.remove('d-none');
                    } else {
                        noReviewsMsg.classList.add('d-none');
                    }
                });
            });
        });
    </script>
</body>
</html>
