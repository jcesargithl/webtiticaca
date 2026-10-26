<?php
require_once __DIR__ . '/includes/blog_helpers.php';

$search_query = trim($_GET['s'] ?? '');
$category_filter = trim($_GET['cat'] ?? '');
$tag_filter = trim($_GET['tag'] ?? '');

$page_title_heading = 'Experiencias de <span>viaje</span>';
$page_subtitle = 'Lea nuestro blog de viajes';

if ($search_query !== '') {
    $posts = search_posts($search_query);
    $page_subtitle = 'Resultados de búsqueda';
    $page_title_heading = 'Buscando: <span>' . htmlspecialchars($search_query) . '</span>';
} elseif ($category_filter !== '') {
    $posts = filter_posts_by_category($category_filter);
    $page_subtitle = 'Categoría';
    $page_title_heading = 'Publicaciones en <span>' . htmlspecialchars($category_filter) . '</span>';
} elseif ($tag_filter !== '') {
    $posts = filter_posts_by_tag($tag_filter);
    $page_subtitle = 'Etiqueta';
    $page_title_heading = 'Etiqueta: <span>' . htmlspecialchars($tag_filter) . '</span>';
} else {
    $posts = get_all_posts(true);
}

// Paginación (6 posts por página)
$per_page = 6;
$total_posts = count($posts);
$total_pages = max(1, (int)ceil($total_posts / $per_page));
$current_page = isset($_GET['p']) ? (int)$_GET['p'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
if ($current_page < 1) {
    $current_page = 1;
}
if ($current_page > $total_pages) {
    $current_page = $total_pages;
}
$offset = ($current_page - 1) * $per_page;
$displayed_posts = array_slice($posts, $offset, $per_page);

// Helper para armar links de paginación manteniendo filtros
function get_page_url($p) {
    $params = $_GET;
    $params['p'] = $p;
    unset($params['page']);
    return 'blog.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Blog - Titicaca Experiences Perú</title>
    <link rel="shortcut icon" href="img/favicon.png" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500&amp;family=Poppins:wght@300;400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="css/plugins.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/translator.css" />
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
    <div class="banner-header section-padding valign bg-img bg-fixed" data-overlay-dark="6" data-background="img/slider/tourmacusani/slide2.jpg">
        <div class="container">
            <div class="row">
                <div class="col-md-12 caption mt-90">
                    <h5><?= $page_subtitle ?></h5>
                    <h1><?= $page_title_heading ?></h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Section -->
    <section class="blog section-padding bg-lightnav">
        <div class="container">

            <!-- Barra de búsqueda rápida y filtros activos -->
            <div class="row mb-30">
                <div class="col-md-6">
                    <form action="blog.php" method="GET" class="d-flex">
                        <input type="text" name="s" value="<?= htmlspecialchars($search_query) ?>" placeholder="Buscar en el blog..." class="form-control" style="border-radius: 4px 0 0 4px; padding: 12px 18px; border: 1px solid #d7ebf0;">
                        <button type="submit" class="btn" style="background: #2095AE; color: #fff; border-radius: 0 4px 4px 0; padding: 0 22px;">
                            <i class="ti-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0 d-flex align-items-center justify-content-md-end">
                    <?php if ($search_query !== '' || $category_filter !== '' || $tag_filter !== ''): ?>
                        <span class="me-2 text-muted">Filtro activo: <strong><?= htmlspecialchars($search_query ?: ($category_filter ?: $tag_filter)) ?></strong></span>
                        <a href="blog.php" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; font-size: 13px;">Limpiar filtro</a>
                    <?php else: ?>
                        <span class="text-muted"><?= $total_posts ?> <?= $total_posts === 1 ? 'artículo publicado' : 'artículos publicados' ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Listado de Posts -->
            <div class="row">
                <?php if (empty($displayed_posts)): ?>
                    <div class="col-md-12 text-center py-5">
                        <i class="ti-info-alt" style="font-size: 48px; color: #2095AE; display: block; margin-bottom: 15px;"></i>
                        <h4>No se encontraron publicaciones</h4>
                        <p>No encontramos artículos con el criterio seleccionado. Intenta con otra búsqueda o categoría.</p>
                        <a href="blog.php" class="butn-dark mt-15"><span>Ver todos los posts</span></a>
                    </div>
                <?php else: ?>
                    <?php foreach ($displayed_posts as $post): 
                        $date_parts = get_spanish_date_parts($post['date'] ?? '');
                        $thumb = !empty($post['image_thumb']) ? $post['image_thumb'] : (!empty($post['image_cover']) ? $post['image_cover'] : 'img/slider/slidepost.jpg');
                        $post_url = 'post.php?slug=' . urlencode($post['slug']);
                        $cat_url = 'blog.php?cat=' . urlencode($post['category'] ?? '');
                    ?>
                        <div class="col-md-4 mb-30">
                            <div class="item">
                                <div class="position-re o-hidden">
                                    <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                                    <div class="date">
                                        <a href="<?= $post_url ?>">
                                            <span><?= htmlspecialchars($date_parts['month_short']) ?></span>
                                            <i><?= htmlspecialchars($date_parts['day']) ?></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="con">
                                    <span class="category">
                                        <a href="<?= $cat_url ?>"><?= htmlspecialchars($post['category'] ?? 'General') ?></a>
                                    </span>
                                    <h5><a href="<?= $post_url ?>"><?= htmlspecialchars($post['title']) ?></a></h5>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Paginador -->
            <?php if ($total_pages > 1): ?>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <ul class="blog-pagination-wrap align-center mb-30 mt-30">
                            <?php if ($current_page > 1): ?>
                                <li><a href="<?= get_page_url($current_page - 1) ?>"><i class="ti-angle-left"></i></a></li>
                            <?php else: ?>
                                <li class="disabled" style="opacity: 0.5;"><a href="javascript:void(0)"><i class="ti-angle-left"></i></a></li>
                            <?php endif; ?>

                            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                <li>
                                    <a href="<?= get_page_url($p) ?>" class="<?= $p === $current_page ? 'active' : '' ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <li><a href="<?= get_page_url($current_page + 1) ?>"><i class="ti-angle-right"></i></a></li>
                            <?php else: ?>
                                <li class="disabled" style="opacity: 0.5;"><a href="javascript:void(0)"><i class="ti-angle-right"></i></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
    
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
                                            <h6>Mabel Gomez</h6> <span>Viajero</span>
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

    <!-- Footer -->
    <footer class="footer clearfix">
        <div class="container">
            <!-- First footer -->
            <div class="first-footer">
                <div class="row">
                    <div class="col-md-12">
                        <div class="links dark footer-contact-links">
                            <div class="footer-contact-links-wrapper">
                                <div class="footer-contact-link-wrapper">
                                    <div class="image-wrapper footer-contact-link-icon">
                                        <div class="icon-footer"> <i class="flaticon-phone-call"></i> </div>
                                    </div>
                                    <div class="footer-contact-link-content">
                                        <h6>Llámanos</h6>
                                        <p>+51 950766721</p>
                                    </div>
                                </div>
                                <div class="footer-contact-links-divider"></div>
                                <div class="footer-contact-link-wrapper">
                                    <div class="image-wrapper footer-contact-link-icon">
                                        <div class="icon-footer"> <i class="flaticon-message"></i> </div>
                                    </div>
                                    <div class="footer-contact-link-content">
                                        <h6>Escríbenos</h6>
                                        <p>gerencia@titicacaexperiencesperu.com</p>
                                    </div>
                                </div>
                                <div class="footer-contact-links-divider"></div>
                                <div class="footer-contact-link-wrapper">
                                    <div class="image-wrapper footer-contact-link-icon">
                                        <div class="icon-footer"> <i class="flaticon-placeholder"></i> </div>
                                    </div>
                                    <div class="footer-contact-link-content">
                                        <h6>Dirección</h6>
                                        <p>Jirón Ayacucho Nº 774, Puno PERÚ</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Second footer -->
            <div class="second-footer">
                <div class="row">
                    <div class="col-md-4 widget-area">
                        <div class="widget clearfix">
                            <div class="footer-logo"> <img class="img-fluid" src="img/logo-titie.png" alt=""> </div>
                            <div class="widget-text">
                                <p>Somos una agencia de viajes especializada en turismo de naturaleza, que ofrece diversos tours temáticos y personalizados acorde a los gustos de nuestros clientes.</p>
                                <div class="social-icons">
                                    <ul class="list-inline">
                                        <li><a href="https://www.instagram.com/titicacaexperiences/"><i class="ti-instagram"></i></a></li>
                                        <li><a href="https://www.tiktok.com/@titicacaexperiences?_t=8bw53UPcddN&_r=1"><i class="ti-twitter"></i></a></li>
                                        <li><a href="https://www.facebook.com/TiticacaExperiences"><i class="ti-facebook"></i></a></li>
                                        <li><a href="https://www.youtube.com/channel/UCTUqjTpfiPUguXkLbQ48VAA"><i class="ti-youtube"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 offset-md-1 widget-area">
                        <div class="widget clearfix usful-links">
                            <h3 class="widget-title">Sitio</h3>
                            <ul>
                                <li><a href="about.html">Nosotros</a></li>
                                <li><a href="tours.html">Destinos</a></li>
                                <li><a href="blog.php">Blog</a></li>
                                <li><a href="contacto.html">Contacto</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 widget-area">
                        <div class="widget clearfix">
                            <h3 class="widget-title">Subscríbete</h3>
                            <p>Regístrate para mantenerte al tanto de nuestro tours y viajes</p>
                            <div class="widget-newsletter">
                                <form action="#">
                                    <input type="email" placeholder="Email " required>
                                    <button type="submit">Enviar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bottom footer -->
            <div class="bottom-footer-text">
                <div class="row copyright">
                    <div class="col-md-12">
                        <p class="mb-0">©2023 <a href="#">Titicaca Experiences</a>. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
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
    <script src="js/translator.js"></script>
</body>
</html>
