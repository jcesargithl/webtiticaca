<?php
require_once __DIR__ . '/includes/blog_helpers.php';

$slug = trim($_GET['slug'] ?? $_GET['id'] ?? '');

$post = get_post_by_slug($slug);

// Si no se encuentra el post, usar el primero o redirigir
if (!$post) {
    $all = get_all_posts(true);
    if (!empty($all)) {
        $post = $all[0];
    } else {
        header('Location: blog.php');
        exit;
    }
}

$date_parts = get_spanish_date_parts($post['date'] ?? '');
$display_date = !empty($post['display_date']) ? $post['display_date'] : $date_parts['full'];
$recent_posts = get_recent_posts(3, $post['slug']);
$categories = get_all_categories();
$all_tags = get_all_tags();
$post_tags = !empty($post['tags']) && is_array($post['tags']) ? $post['tags'] : [];
$cover_img = !empty($post['image_cover']) ? $post['image_cover'] : (!empty($post['image_thumb']) ? $post['image_thumb'] : 'img/slider/slidepost.jpg');
$author_img = !empty($post['author_avatar']) ? $post['author_avatar'] : 'img/team/perfil.png';
$author_name = !empty($post['author']) ? $post['author'] : 'Ernesto Aliaga';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title><?= htmlspecialchars($post['title']) ?> - Titicaca Experiences Perú</title>
    <link rel="shortcut icon" href="img/favicon.png" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500&amp;family=Poppins:wght@300;400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="css/plugins.css" />
    <link rel="stylesheet" href="css/style.css" />
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
    <nav class="navbar navbar-expand-md">
        <div class="container">
            <!-- Logo -->
            <div class="logo-wrapper">
                <a class="logo" href="index.html"> <img src="img/logo-titie.png" class="logo-img" alt=""> </a>
            </div>
            <!-- Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"><i class="ti-menu"></i></span> </button>
            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Inicio</a></li>
                    <li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Destinos <i class="ti-angle-down"></i></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-submenu dropdown"> <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" href="#"><span>Cusco <i class="ti-angle-right"></i></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="tour-cusco.html" class="dropdown-item"><span>Diarios</span></a></li>
                                    <li><a href="tour-aves.html" class="dropdown-item"><span>Observación de aves</span></a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu dropdown"> <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" href="#"><span>Puno <i class="ti-angle-right"></i></span></a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu dropdown"> <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" href="#"><span>Diarios <i class="ti-angle-right"></i></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="tour-taquile.html" class="dropdown-item"><span>Taquile</span></a></li>
                                            <li><a href="tour-amantani.html" class="dropdown-item"><span>Amantani</span></a></li>
                                            <li><a href="tour-ruta-aymara.html" class="dropdown-item"><span>Ruta Aymara</span></a></li>
                                            <li><a href="tour-ruta-quechua.html" class="dropdown-item"><span>Ruta Quechua</span></a></li>
                                            <li><a href="tour-sillustani.html" class="dropdown-item"><span>Sillustani</span></a></li>
                                            <li><a href="tour-canoas.html" class="dropdown-item"><span>Canoas Polinésicas</span></a></li>
                                            <li><a href="tour-uros.html" class="dropdown-item"><span>Uros</span></a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu dropdown"> <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" href="#"><span>Aventura <i class="ti-angle-right"></i></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="tour-canoas.html" class="dropdown-item"><span>Canoas Polinésicas</span></a></li>
                                            <li><a href="tour-kayak.html" class="dropdown-item"><span>Kayak</span></a></li>
                                            <li><a href="tour-rapel.html" class="dropdown-item"><span>Rapel</span></a></li>
                                            <li><a href="tour-macusani.html" class="dropdown-item"><span>Macusani</span></a></li>
                                            <li><a href="tour-buceo.html" class="dropdown-item"><span>Buceo con la rana gigante</span></a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu dropdown"> <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" href="#"><span>Místico <i class="ti-angle-right"></i></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="tour-rutaaymistico.html" class="dropdown-item"><span>Ruta Aymara, Místico</span></a></li>
                                        </ul>
                                    </li>
                                    <li><a href="tour-aves.html" class="dropdown-item"><span>Observación de aves</span></a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu dropdown"> <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" href="#"><span>Bolivia <i class="ti-angle-right"></i></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="tour-salar.html" class="dropdown-item"><span>Salar de Uyuni</span></a></li>
                                    <li><a href="tour-islasol.html" class="dropdown-item"><span>Isla del Sol</span></a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu dropdown"> <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" href="#"><span>Paquetes <i class="ti-angle-right"></i></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="tour-peru.html" class="dropdown-item"><span>Paquete Perú</span></a></li>
                                    <li><a href="tour-cusco.html" class="dropdown-item"><span>Paquete Cusco</span></a></li>
                                    <li><a href="tour-puno.html" class="dropdown-item"><span>Paquete Puno</span></a></li>
                                    <li><a href="tour-bolivia.html" class="dropdown-item"><span>Paquete Bolivia</span></a></li>
                                    <li><a href="tour-aves.html" class="dropdown-item"><span>Observación de aves</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="about.html">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="galeria.html">Galería</a></li>
                    <li class="nav-item active"><a class="nav-link active" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacto.html">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Banner -->
    <div class="banner-header section-padding back-position-center valign bg-img bg-fixed" data-overlay-dark="5" data-background="<?= htmlspecialchars($cover_img) ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-7 text-left caption mt-90">
                    <h5><a href="blog.php">Blog</a> / <?= htmlspecialchars($post['category'] ?? 'Publicación') ?></h5>
                    <h1><?= htmlspecialchars($post['title']) ?></h1>
                    <div class="post">
                        <div class="author">
                            <img src="<?= htmlspecialchars($author_img) ?>" alt="<?= htmlspecialchars($author_name) ?>" class="avatar">
                            <span><?= htmlspecialchars($author_name) ?></span>
                        </div>
                        <div class="date-comment">
                            <i class="ti-calendar"></i> <?= htmlspecialchars($display_date) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Post Detail Content -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <!-- Main Post Content -->
                <div class="col-md-8"> 
                    <?php if (!empty($post['image_cover'])): ?>
                        <img src="<?= htmlspecialchars($post['image_cover']) ?>" class="mb-30" alt="<?= htmlspecialchars($post['title']) ?>" style="border-radius: 6px; width: 100%;">
                    <?php endif; ?>

                    <h2><?= htmlspecialchars($post['title']) ?></h2>

                    <!-- Texto del artículo -->
                    <div class="post-body-content">
                        <?= $post['content'] ?>
                    </div>

                    <!-- Caja de Cita / Quote si existe -->
                    <?php if (!empty($post['quote'])): ?>
                        <blockquote>
                            <p><?= htmlspecialchars($post['quote']) ?></p>
                            <?php if (!empty($post['quote_author'])): ?>
                                <cite><?= htmlspecialchars($post['quote_author']) ?></cite>
                            <?php endif; ?>
                        </blockquote>
                    <?php endif; ?>

                    <!-- Tags del Post al final -->
                    <?php if (!empty($post_tags)): ?>
                        <div class="mt-30 pt-20" style="border-top: 1px solid #eef2f5;">
                            <span class="text-muted me-2" style="font-weight: 600;"><i class="ti-tag"></i> Etiquetas:</span>
                            <?php foreach ($post_tags as $t): ?>
                                <a href="blog.php?tag=<?= urlencode($t) ?>" class="badge bg-light text-dark me-1 p-2" style="font-size: 13px; text-decoration: none; border: 1px solid #d7ebf0;">
                                    <?= htmlspecialchars($t) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
               
                <!-- Sidebar -->
                <div class="col-md-4">
                    <div class="blog2-sidebar row">

                        <!-- 1. Buscador Funcional -->
                        <div class="col-md-12">
                            <div class="widget search">
                                <form action="blog.php" method="GET">
                                    <input type="text" name="s" placeholder="Buscar ..." required>
                                    <button type="submit"><i class="ti-search" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        </div>

                        <!-- 2. Posts Recientes Funcionales -->
                        <?php if (!empty($recent_posts)): ?>
                            <div class="col-md-12">
                                <div class="widget">
                                    <div class="widget-title">
                                        <h6>Posts recientes</h6>
                                    </div>
                                    <ul class="recent">
                                        <?php foreach ($recent_posts as $r): 
                                            $r_thumb = !empty($r['image_thumb']) ? $r['image_thumb'] : (!empty($r['image_cover']) ? $r['image_cover'] : 'img/slider/slidepost.jpg');
                                            $r_url = 'post.php?slug=' . urlencode($r['slug']);
                                        ?>
                                            <li>
                                                <div class="thum">
                                                    <a href="<?= $r_url ?>">
                                                        <img src="<?= htmlspecialchars($r_thumb) ?>" alt="<?= htmlspecialchars($r['title']) ?>">
                                                    </a>
                                                </div> 
                                                <a href="<?= $r_url ?>"><?= htmlspecialchars($r['title']) ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 3. Categorías Dinámicas con contador -->
                        <?php if (!empty($categories)): ?>
                            <div class="col-md-12">
                                <div class="widget">
                                    <div class="widget-title">
                                        <h6>Categorías</h6>
                                    </div>
                                    <ul>
                                        <?php foreach ($categories as $cat_name => $count): ?>
                                            <li>
                                                <a href="blog.php?cat=<?= urlencode($cat_name) ?>">
                                                    <i class="ti-angle-right"></i><?= htmlspecialchars($cat_name) ?> <span class="text-muted" style="font-size: 12px;">(<?= $count ?>)</span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 4. Tags Dinámicas -->
                        <?php if (!empty($all_tags)): ?>
                            <div class="col-md-12">
                                <div class="widget">
                                    <div class="widget-title">
                                        <h6>Tags</h6>
                                    </div>
                                    <ul class="tags">
                                        <?php foreach ($all_tags as $tg): ?>
                                            <li><a href="blog.php?tag=<?= urlencode($tg) ?>"><?= htmlspecialchars($tg) ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

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
</body>
</html>
