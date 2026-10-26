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
           ESTILOS EXCLUSIVOS, ELEGANTES Y CUADRADOS - TITICACA EXPERIENCES
        ============================================================== */
        :root {
            --brand-navy: #0f2454;
            --brand-cyan: #2095AE;
            --brand-gold: #c5a880;
            --star-gold: #f59e0b;
        }

        /* Banner Header */
        .banner-header.reviews-banner {
            background-position: center center;
            background-size: cover;
            min-height: 380px;
            position: relative;
        }

        /* Pestañas de Filtro por Plataforma (Cuadrados y sin números) */
        .reviews-filter-nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 45px;
        }
        .reviews-filter-btn {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            padding: 12px 26px;
            border-radius: 0 !important; /* Totalmente Cuadrado */
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            text-transform: none;
        }
        .reviews-filter-btn:hover {
            border-color: var(--brand-navy);
            color: var(--brand-navy);
            background: #f8fafc;
        }
        .reviews-filter-btn.active {
            background: var(--brand-navy);
            border-color: var(--brand-navy);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(15, 36, 84, 0.25);
        }

        /* Tarjeta de Reseña (Review Card Cuadrado) */
        .review-card-item {
            transition: opacity 0.35s ease, transform 0.35s ease;
        }
        .review-card-item.is-hidden {
            display: none !important;
        }
        .review-card {
            background: #ffffff;
            border-radius: 0 !important; /* Totalmente Cuadrado */
            border: 1px solid rgba(15, 36, 84, 0.08);
            box-shadow: 0 6px 20px rgba(15, 36, 84, 0.04);
            padding: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px rgba(15, 36, 84, 0.1);
            border-color: var(--brand-cyan);
        }

        /* Prevenir cualquier pseudoelemento o triángulo desalineado */
        .review-card::before,
        .review-card::after,
        .review-card-item::before,
        .review-card-item::after,
        .review-card-stars::before,
        .review-card-stars::after,
        .review-card-stars *::before,
        .review-card-stars *::after {
            display: none !important;
            content: none !important;
            border: none !important;
            background: none !important;
        }

        /* Ícono de Comillas (Moderno y Tipográfico, no "66") */
        .review-card-quote-icon {
            position: absolute;
            top: 22px;
            right: 24px;
            color: var(--brand-cyan);
            opacity: 0.16;
            line-height: 1;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .review-card:hover .review-card-quote-icon {
            opacity: 0.28;
        }

        .review-author-thumb {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--brand-cyan);
            box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        }
        .review-card-stars {
            color: var(--star-gold);
            font-size: 13px;
            margin-bottom: 4px;
            line-height: 1;
        }
        .review-star {
            display: inline-block;
            line-height: 1;
        }
        .review-author-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 15px;
            color: var(--brand-navy);
            margin-bottom: 3px;
            line-height: 1.3;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
        }
        .review-author-role {
            font-size: 12px;
            color: #64748b;
            font-weight: 400;
            line-height: 1.2;
        }
        .review-card-text {
            font-size: 14px;
            color: #334155;
            line-height: 1.7;
            font-family: 'Barlow', sans-serif;
            font-weight: 400;
            margin-top: 18px;
            margin-bottom: 0;
            flex-grow: 1;
        }

        /* Sección de Invitación / CTA (Fondo igual al footer y Cuadrada) */
        .leave-review-section {
            background: #0f2454 !important; /* Mismo azul que el footer */
            border-radius: 0 !important; /* Cuadrado */
            padding: 50px 40px;
            color: #ffffff;
            margin-top: 60px;
            position: relative;
            border-left: 4px solid #2095AE;
            box-shadow: 0 10px 30px rgba(15, 36, 84, 0.15);
        }
        .badge-inspirar {
            background-color: #2095AE !important; /* Celeste de la plantilla */
            color: #ffffff !important; /* Letras blancas */
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
            border-radius: 0 !important; /* Cuadrado */
            margin-bottom: 12px;
        }

        /* Botones de acción en la sección CTA (Totalmente Cuadrados) */
        .btn-leave-action {
            border-radius: 0 !important; /* Totalmente Cuadrado */
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 13px;
            padding: 12px 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            text-decoration: none;
            width: 100%;
        }
        .btn-leave-action.btn-google {
            background: #ffffff;
            color: #0f2454;
            border: 1px solid #ffffff;
        }
        .btn-leave-action.btn-google:hover {
            background: #f8fafc;
            color: #0f2454;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,255,255,0.2);
        }
        .btn-leave-action.btn-tripadvisor {
            background: #00aa6c;
            color: #ffffff;
            border: 1px solid #00aa6c;
        }
        .btn-leave-action.btn-tripadvisor:hover {
            background: #008f5a;
            color: #ffffff;
            border-color: #008f5a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 170, 108, 0.3);
        }
        .btn-leave-action.btn-whatsapp {
            background: transparent;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .btn-leave-action.btn-whatsapp:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
            color: #ffffff;
            transform: translateY(-2px);
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

    <!-- Sección Principal de Reseñas -->
    <section class="section-padding" style="background-color: #f8fafc; padding-top: 60px; padding-bottom: 90px;">
        <div class="container">
            
            <!-- 1. Barra de Filtros Interactivos (Cuadrados y sin números) -->
            <div class="reviews-filter-nav">
                <button type="button" class="reviews-filter-btn active" data-filter="all">
                    <span>Todas las Reseñas</span>
                </button>
                <button type="button" class="reviews-filter-btn" data-filter="google">
                    <svg width="13" height="13" viewBox="0 0 24 24" style="vertical-align: -1px; margin-right: 2px;">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Google Maps</span>
                </button>
                <button type="button" class="reviews-filter-btn" data-filter="tripadvisor">
                    <svg width="14" height="14" viewBox="0 0 713.5 713.5" style="vertical-align: -2px; margin-right: 2px;">
                        <circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/>
                        <path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/>
                    </svg>
                    <span>Tripadvisor</span>
                </button>
                <button type="button" class="reviews-filter-btn" data-filter="web">
                    <i class="ti-world" style="font-size: 13px; margin-right: 2px;"></i>
                    <span>Web</span>
                </button>
            </div>

            <!-- 2. Grid de Tarjetas de Reseña (Cuadradas y con Distintivos Estilo Testimonios) -->
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
                    ?>
                        <div class="col-lg-4 col-md-6 mb-30 review-card-item" data-source="<?= $source ?>">
                            <div class="review-card">
                                
                                <!-- Comillas elegantes en SVG (no el glifo "66") -->
                                <div class="review-card-quote-icon" title="Cita">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                    </svg>
                                </div>

                                <!-- Cabecera del Autor (con distintivo idéntico a sección testimonio) -->
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $photo ?>" alt="<?= $name ?>" class="review-author-thumb" onerror="this.src='img/team/07.png'">
                                    <div>
                                        <!-- Estrellas doradas limpias sin pseudoelementos -->
                                        <div class="review-card-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="review-star" style="color: <?= $i <= $rating ? '#f59e0b' : '#cbd5e1' ?>; font-size: 14px; margin-right: 1px;">★</span>
                                            <?php endfor; ?>
                                        </div>

                                        <!-- Nombre + Badge estilo sección testimonio (Imagen 5) -->
                                        <h4 class="review-author-name">
                                            <?= $name ?>
                                            <?php if ($source === 'google'): ?>
                                                <a href="<?= !empty($reviewUrl) ? htmlspecialchars($reviewUrl) : $google_profile_url ?>" target="_blank" rel="noopener noreferrer" class="google-badge-pill" title="Ver reseña en Google Maps">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: -1px; margin-right: 3px;"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>Google
                                                </a>
                                            <?php elseif ($source === 'tripadvisor'): ?>
                                                <a href="<?= !empty($reviewUrl) ? htmlspecialchars($reviewUrl) : $tripadvisor_profile_url ?>" target="_blank" rel="noopener noreferrer" class="tripadvisor-badge-pill" title="Ver reseña en Tripadvisor">
                                                    <svg width="14" height="14" viewBox="0 0 713.5 713.5" style="vertical-align: -2px; margin-right: 3px;"><circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/><path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/></svg>Tripadvisor
                                                </a>
                                            <?php else: ?>
                                                <span class="review-badge-pill" title="Reseña Web">
                                                    <i class="ti-world" style="font-size: 10px; margin-right: 3px;"></i>Web
                                                </span>
                                            <?php endif; ?>
                                        </h4>
                                        <div class="review-author-role"><?= $role ?></div>
                                    </div>
                                </div>

                                <!-- Contenido / Texto de la Reseña -->
                                <p class="review-card-text">
                                    "<?= $text ?>"
                                </p>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Mensaje cuando un filtro no tiene resultados -->
            <div id="no-reviews-msg" class="text-center py-5 d-none">
                <div class="p-4 bg-white shadow-sm border d-inline-block" style="border-radius: 0;">
                    <i class="ti-info-alt mb-2" style="font-size: 28px; color: #2095AE;"></i>
                    <h5 class="mb-1" style="color: #0f2454;">No se encontraron reseñas en esta categoría</h5>
                    <p class="text-muted small mb-0">Selecciona "Todas las Reseñas" para ver el catálogo completo.</p>
                </div>
            </div>

            <!-- 3. Sección de Invitación a Calificar / CTA (Fondo igual al footer, Cuadrada y con Botón Tripadvisor) -->
            <div class="leave-review-section">
                <div class="row align-items-center">
                    <div class="col-lg-7 mb-4 mb-lg-0">
                        <span class="badge-inspirar">Tu opinión nos inspira</span>
                        <h2 style="color: #ffffff; font-size: 30px; font-weight: 700; margin: 4px 0 12px 0; font-family: 'Poppins', sans-serif;">¿Viajaste recientemente con nosotros?</h2>
                        <p style="color: #cbd5e1; font-size: 14px; margin-bottom: 0; max-width: 580px; line-height: 1.6;">
                            Ayuda a otros viajeros de todo el mundo a descubrir la magia del Lago Titicaca y los Andes peruanos compartiendo tu experiencia sincera.
                        </p>
                    </div>
                    <div class="col-lg-5 text-center text-lg-end">
                        <div class="d-flex flex-column gap-2 justify-content-lg-end">
                            <!-- Botón Google Maps Cuadrado -->
                            <a href="<?= htmlspecialchars($google_profile_url) ?>" target="_blank" rel="noopener noreferrer" class="btn-leave-action btn-google">
                                <svg width="14" height="14" viewBox="0 0 24 24" style="vertical-align: -1px; margin-right: 4px;">
                                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/>
                                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                    <path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/>
                                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                                </svg>
                                <span>Calificar en Google Maps</span>
                            </a>
                            
                            <!-- Botón Tripadvisor Cuadrado -->
                            <a href="<?= htmlspecialchars($tripadvisor_profile_url) ?>" target="_blank" rel="noopener noreferrer" class="btn-leave-action btn-tripadvisor">
                                <svg width="15" height="15" viewBox="0 0 713.5 713.5" style="vertical-align: -2px; margin-right: 4px;">
                                    <circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/>
                                    <path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/>
                                </svg>
                                <span>Calificar en Tripadvisor</span>
                            </a>

                            <!-- Botón WhatsApp Cuadrado -->
                            <a href="https://wa.me/51950766721?text=Hola,%20deseo%20compartir%20mi%20rese%C3%B1a%20sobre%20el%20tour" target="_blank" rel="noopener noreferrer" class="btn-leave-action btn-whatsapp">
                                <i class="ti-comment-alt me-1"></i>
                                <span>Enviar testimonio por WhatsApp</span>
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
    <script src="js/owl.carousel.min.js"></script>
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
