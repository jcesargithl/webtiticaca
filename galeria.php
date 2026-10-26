<?php
// galeria.php - Galería Dinámica de Imágenes y Videos para Titicaca Experiences Perú
require_once __DIR__ . '/includes/gallery_helpers.php';

$images = get_gallery_images();
$videos = get_gallery_videos();
$initial_show = 12; // Número de imágenes visibles inicialmente
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Galería de Imágenes y Videos - Titicaca Experiences Perú</title>
    <link rel="shortcut icon" href="img/favicon.png" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500&amp;family=Poppins:wght@300;400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="css/plugins.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/translator.css" />
    <style>
        .gallery-item {
            transition: transform 0.3s ease, opacity 0.4s ease;
        }
        .gallery-box {
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .gallery-img img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-box:hover .gallery-img img {
            transform: scale(1.05);
        }
        .btn-load-more-gal {
            background: linear-gradient(135deg, #2095AE 0%, #156c7e 100%);
            color: #fff !important;
            border: none;
            padding: 13px 36px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 20px rgba(32, 149, 174, 0.35);
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-load-more-gal:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(32, 149, 174, 0.45);
            background: linear-gradient(135deg, #17788c 0%, #0e4c58 100%);
        }
        .vid-box-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            background: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .vid-box-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        .vid-thumb-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
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

    <!-- Navbar -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- Header Banner -->
    <div class="banner-header section-padding valign bg-img bg-fixed" data-overlay-dark="6" data-background="img/slider/2.jpg">
        <div class="container">
            <div class="row">
                <div class="col-md-12 caption mt-90">
                    <h5>Imágenes y videos</h5>
                    <h1><span>Galería  </span> Imágenes & Videos </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Gallery -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mb-30">
                    <div class="section-subtitle">Momentos Inolvidables</div>
                    <div class="section-title">Galería de <span>Imágenes</span></div>
                </div>

                <?php if (empty($images)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Pronto agregaremos nuevas fotos de nuestras experiencias.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($images as $index => $img): 
                        // Ritmo de diseño: 3 columnas de 4, luego 2 columnas de 6, repitiendo cíclicamente
                        $col_class = ($index % 5 < 3) ? 'col-md-4' : 'col-md-6';
                        $is_hidden = ($index >= $initial_show);
                        $title = !empty($img['title']) ? $img['title'] : '';
                        $alt = !empty($img['alt']) ? $img['alt'] : ($title ?: 'Titicaca Experiences');
                    ?>
                        <div class="<?= $col_class ?> gallery-item <?= $is_hidden ? 'gallery-item-extra' : '' ?>" <?= $is_hidden ? 'style="display: none;"' : '' ?>>
                            <a href="<?= htmlspecialchars($img['src']) ?>" title="<?= htmlspecialchars($title) ?>" class="img-zoom">
                                <div class="gallery-box">
                                    <div class="gallery-img">
                                        <img src="<?= htmlspecialchars($img['src']) ?>" class="img-fluid mx-auto d-block" alt="<?= htmlspecialchars($alt) ?>" loading="lazy">
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <?php if (count($images) > $initial_show): ?>
                        <div class="col-12 text-center mt-40" id="loadMoreWrap">
                            <button type="button" class="btn-load-more-gal" id="btnLoadMoreGallery">
                                <i class="ti-reload"></i>
                                <span>Cargar más fotos (<strong id="remainingCount"><?= count($images) - $initial_show ?></strong>)</span>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Video Gallery -->
    <?php if (!empty($videos)): ?>
    <section class="section-padding bg-lightnav">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mb-30">
                    <div class="section-subtitle">Videos</div>
                    <div class="section-title">Galería de <span>Videos</span></div>
                </div>

                <?php foreach ($videos as $v): 
                    $v_thumb = !empty($v['thumb']) ? $v['thumb'] : get_video_thumbnail($v['url']);
                    $v_title = !empty($v['title']) ? $v['title'] : 'Video Titicaca Experiences';
                ?>
                <div class="col-md-6 mb-30">
                    <div class="vid-box-card">
                        <div class="vid-area mb-0">
                            <div class="vid-icon">
                                <img src="<?= htmlspecialchars($v_thumb) ?>" alt="<?= htmlspecialchars($v_title) ?>" class="vid-thumb-img">
                                <a class="video-gallery-button vid" href="<?= htmlspecialchars($v['url']) ?>">
                                    <span class="video-gallery-polygon">
                                        <i class="ti-control-play"></i>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <?php if (!empty($v['title'])): ?>
                            <div class="p-3 text-center bg-white">
                                <h5 class="mb-0 fw-semibold" style="color: #0f2454; font-size: 1.15rem;">
                                    <?= htmlspecialchars($v['title']) ?>
                                </h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="background bg-img bg-fixed section-padding pb-0" data-background="img/slider/home-review.jpg" data-overlay-dark="5">
            <div class="container">
                <div class="row">
                    <!-- Call Now  -->
                    <div class="col-md-5 mb-30 mt-30">
                        <p><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i><i class="star-rating"></i></p>
                        <h5>¡Ofrecemos los mejores destinos turísticos para que conozcas y disfrutes el Lago Titicaca!</h5>
                        <div class="phone-call mb-10">
                            <div class="icon color-1"><span class="flaticon-phone-call"></span></div>
                            <div class="text">
                                <p class="color-1">Contáctanos</p> <a class="color-1" href="tel:+51 950766721">+51 950766721</a>
                            </div>
                        </div>
                    </div>
                    <!-- Booking From -->
                    <div class="col-md-5 offset-md-2">
                        <div class="testimonials-box">
                            <div class="head-box">
                                <h6>Testimonios</h6>
                                <h4>Reseñas de nuestros viajeros</h4>
                            </div>
                            <div class="owl-carousel owl-theme">
                                <div class="item">
                                    <p>Excelente servicio, en todos los sentidos. De inicio a final. Amabilidad, confianza y muuuuucha buena vibra ✨🌏.</p>
                                    <div class="info">
                                        <div class="author-img"> <img src="img/team/07.png" alt=""> </div>
                                        <div class="cont">
                                            <div class="rating"> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> </div>
                                            <h6>Nidia Perez</h6> <span>Viajero</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <p>Excelente servicio, el personal muy amable.</p>
                                    <div class="info">
                                        <div class="author-img"> <img src="img/team/08.png" alt=""> </div>
                                        <div class="cont">
                                            <div class="rating"> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> </div>
                                            <h6>Eberth Calsin</h6> <span>Viajero</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item">
                                    <p>Muy agradecida con su servicio de primera, lo recomiendo.</p>
                                    <div class="info">
                                        <div class="author-img"> <img src="img/team/09.png" alt=""> </div>
                                        <div class="cont">
                                            <div class="rating"> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> </div>
                                            <h6>Silvia Ichillumpa</h6> <span>Viajero</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Clients -->
    <section class="clients">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <div class="owl-carousel owl-theme">
                        <div class="clients-logo">
                            <a href="#0"><img src="img/clients/1.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="img/clients/2.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="img/clients/3.png" alt=""></a>
                        </div>
                        <div class="clients-logo">
                            <a href="#0"><img src="img/clients/4.png" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- jQuery & Plugins -->
    <script src="js/jquery-3.6.3.min.js"></script>
    <script src="js/jquery-migrate-3.0.0.min.js"></script>
    <script src="js/modernizr-2.6.2.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/jquery.isotope.v3.0.2.js"></script>
    <script src="js/pace.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scrollIt.min.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/jquery.magnific-popup.js"></script>
    <script src="js/YouTubePopUp.js"></script>
    <script src="js/select2.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/smooth-scroll.min.js"></script>
    <script src="js/custom.js"></script>
    <!-- Translator Engine -->
    <script src="js/translator.js"></script>

    <!-- Script de Carga Progresiva ("Cargar Más Fotos") -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnLoadMore = document.getElementById('btnLoadMoreGallery');
            if (!btnLoadMore) return;

            const batchSize = 6; // Cantidad de fotos adicionales a mostrar por clic

            btnLoadMore.addEventListener('click', function (e) {
                e.preventDefault();
                const hiddenItems = Array.from(document.querySelectorAll('.gallery-item-extra'));
                const toShow = hiddenItems.slice(0, batchSize);

                toShow.forEach(item => {
                    item.style.opacity = '0';
                    item.style.display = '';
                    item.classList.remove('gallery-item-extra');
                    setTimeout(() => {
                        item.style.transition = 'opacity 0.4s ease';
                        item.style.opacity = '1';
                    }, 40);
                });

                // Refrescar el visor de imagen Magnific Popup
                if (window.jQuery && jQuery.fn.magnificPopup) {
                    jQuery('.img-zoom').magnificPopup({
                        type: "image",
                        closeOnContentClick: true,
                        mainClass: "mfp-fade",
                        gallery: {
                            enabled: true,
                            navigateByImgClick: true,
                            preload: [0, 1]
                        }
                    });
                }

                const remaining = document.querySelectorAll('.gallery-item-extra').length;
                const remainingSpan = document.getElementById('remainingCount');
                if (remainingSpan) {
                    remainingSpan.innerText = remaining;
                }

                if (remaining === 0) {
                    const wrap = document.getElementById('loadMoreWrap');
                    if (wrap) {
                        wrap.innerHTML = '<p class="text-muted small mt-3"><i class="ti-check text-success me-1"></i>Has visto todas las fotos de nuestra galería</p>';
                    }
                }
            });
        });
    </script>
</body>

</html>
