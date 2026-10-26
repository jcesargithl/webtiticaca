<?php
require_once __DIR__ . '/includes/blog_helpers.php';
$recent_home_posts = get_recent_posts(3); // Solo los ultimos 3 posts
?>
<!DOCTYPE html>
<html lang="zxx">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Titicaca Experiences Perú</title>
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
    <?php include __DIR__ . "/includes/navbar.php"; ?>

    <!-- Header Video -->
    <header class="header">
        <div class="video-fullscreen-wrap">
            <!-- The opacity on the image is made with "data-overlay-dark="number". You can change it using the numbers 0-9. -->
            <div class="video-fullscreen-video" data-overlay-dark="5">
                <video playsinline="" autoplay="" loop="" muted="">
                    <source src="video/travel-titi2.webm" type="video/mp4" autoplay="" loop="" muted="">
                    <!--<source src="https://duruthemes.com/demo/html/travol/travel-video.webm" type="video/webm" autoplay="" loop="" muted="">-->
                </video>
            </div>
            <div class="v-middle caption overlay">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <h4>no se recuerdan los dìas, se recuerdan los momentos</h4>
                                <h1>Explora el mundo con <span>TITICACA Experiences</span></h1>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </header>
    <!-- Tour Search -->
    <!--
    <div class="booking-wrapper">
        <div class="container">
            <div class="tour-inner clearfix form-inline justify-content-center">
                <form action="https://duruthemes.com/demo/html/travol/multipage-video/tours.html" class="form1 clearfix">
                    <div class="col1 c1">
                        <div class="input2_wrapper">
                            <label>Where to?</label>
                            <div class="input2_inner">
                                <input type="text" class="form-control input" placeholder="Where to?">
                            </div>
                        </div>
                    </div>
                    <div class="col1 c2">
                        <div class="select1_wrapper">
                            <label>Destinations</label>
                            <div class="select1_inner">
                                <select class="select2 select" style="width: 100%">
                                    <option value="0">Destinations</option>
                                    <option value="1">Greece</option>
                                    <option value="2">London</option>
                                    <option value="3">Maldives</option>
                                    <option value="4">Paris</option>
                                    <option value="5">Rome</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col1 c4">
                        <div class="select1_wrapper">
                            <label>Duration</label>
                            <div class="select1_inner">
                                <select class="select2 select" style="width: 100%">
                                    <option value="0">Duration</option>
                                    <option value="1">1 Day Tour</option>
                                    <option value="2">2-4 Days Tour</option>
                                    <option value="3">5-7 Days Tour</option>
                                    <option value="4">7+ Days Tour</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col1 c5">
                        <button type="submit" class="btn-form1-submit"><i class="ti-search"></i> Find Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    -->
    <!-- About -->
    <section class="about cover section-padding">
        <div class="container">
            
            <div class="row">
                <div class="col-md-6 mb-30 animate-box" data-animate-effect="fadeInUp">
                    <div class="section-subtitle">La mejor agencia de viajes</div>
                    <div class="section-title">Descubre el <span>titicaca</span> con nosotros</div>
                    <p>Somos una agencia de viajes especializada en turismo de naturaleza, que ofrece diversos tours temáticos y personalizados acorde a los gustos de nuestros clientes.</p>
                    <p> Además somos responsables con el territorio y todas las cualidades que hacen que un destino sea mágico, para así crear conciencia en nuestros clientes con el medio amiente, protegiendo ecosistemas, costumbres y cultura.</p>
                    <p>
                        <strong style="color: #2095AE; font-size: 18px; font-weight: 700; display: block; margin-bottom: 6px;">Misión:</strong>
                        Brindar experiencias turísticas auténticas y personalizadas, promoviendo el turismo sostenible, la conservación de la biodiversidad, la valoración cultural y el desarrollo de las comunidades locales, mediante prácticas responsables alineadas con los principios de sostenibilidad de Biosphere.
                    </p>
                    <p>
                        <strong style="color: #2095AE; font-size: 18px; font-weight: 700; display: block; margin-bottom: 6px;">Visión:</strong>
                        Para el año 2027, ser una agencia referente en turismo de naturaleza sostenible en el sur del Perú, reconocida por su compromiso con la conservación ambiental, la responsabilidad social y la mejora continua de su gestión sostenible.
                    </p>
                    <ul class="list-unstyled about-list mb-30">
                        <li>
                            <div class="about-list-icon"> <span class="ti-check"></span> </div>
                            <div class="about-list-text">
                                <p>6 años de experiencias</p>
                            </div>
                        </li>
                        <li>
                            <div class="about-list-icon"> <span class="ti-check"></span> </div>
                            <div class="about-list-text">
                                <p>40+ Destinos turísticos</p>
                            </div>
                        </li>
                    </ul>
                    <!-- Info -->
                    <div class="phone-call mb-30">
                        <div class="icon"><span class="flaticon-phone-call"></span></div>
                        <div class="text">
                            <p>Información</p> <a href="tel:51950766721">+51 950766721 </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 offset-md-1 animate-box" data-animate-effect="fadeInUp">
                    <div class="img-exp">
                        <div class="about-img">
                            <div class="img"> <a href="tour-canoas.html"> <img src="img/about2.jpg" class="img-fluid" alt=""> </a></div>
                        </div>
                        <div id="circle">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="300px" height="300px" viewBox="0 0 300 300" enable-background="new 0 0 300 300" xml:space="preserve">
                                <defs>
                                    <path id="circlePath" d=" M 150, 150 m -60, 0 a 60,60 0 0,1 120,0 a 60,60 0 0,1 -120,0 " />
                                </defs>
                                <circle cx="150" cy="100" r="75" fill="none" />
                                <g>
                                    <use xlink:href="#circlePath" fill="none" />
                                    <text fill="#0f2454">
                                        <textPath xlink:href="#circlePath">.Titicaca .Experiences .Puno .Perú</textPath>
                                    </text>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Tours 1 -->
    <section class="tours1 section-padding bg-lightnav" data-scroll-index="1">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-subtitle"><span>Escoge tu lugar</span></div>
                    <div class="section-title">Tours <span>Populares</span></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="item">
                        <div class="position-re o-hidden"> <img src="img/tours/tourportada1.jpg" alt=""> </div> <!--<span class="category"><a href="#0">$2.500</a></span>-->
                        <div class="con">
                            <div class="rating"> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i> <i class="star active"></i>
                                <div class="reviews-count"><!--(2 Reviews)--></div>
                            </div>
                            <h5><a href="tour-peru.html">Paquete Perú</a></h5>
                            <div class="line"></div>
                            <div class="row facilities">
                                <div class="col col-md-12">
                                    <ul>
                                        <li><i class="ti-time"></i> 14 Días</li>
                                        <li><i class="ti-user"></i> 5+</li>
                                        <li><i class="ti-location-pin"></i> Perú</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <div class="position-re o-hidden"><img src="img/tours/tourportada2.jpg" alt=""></div> <!--<span class="category"><a href="#0">$1.300</a></span>-->
                        <div class="con">
                            <h5><a href="tour-puno.html">Paquete Puno</a> </h5>
                            <div class="line"></div>
                            <div class="row facilities">
                                <div class="col col-md-12">
                                    <ul>
                                        <li><i class="ti-time"></i> 4 Días</li>
                                        <li><i class="ti-user"></i> 5+</li>
                                        <li><i class="ti-location-pin"></i> Puno</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <div class="position-re o-hidden"><img src="img/tours/tourportada3.jpg" alt=""></div> <!--<span class="category"><a href="#0">$400</a></span>-->
                        <div class="con">
                            <h5><a href="tour-cusco.html">Paquete Cusco</a> </h5>
                            <div class="line"></div>
                            <div class="row facilities">
                                <div class="col col-md-12">
                                    <ul>
                                        <li><i class="ti-time"></i> 5 Días</li>
                                        <li><i class="ti-user"></i> 5+</li>
                                        <li><i class="ti-location-pin"></i> Cusco</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <div class="position-re o-hidden"><img src="img/tours/tourportada4.jpg" alt=""></div><!--<span class="category"><a href="#">$500</a></span>-->
                        <div class="con">
                            <h5><a href="tour-bolivia.html">Paquete Bolivia</a> </h5>
                            <div class="line"></div>
                            <div class="row facilities">
                                <div class="col col-md-12">
                                    <ul>
                                        <li><i class="ti-time"></i> 5 Días</li>
                                        <li><i class="ti-user"></i> 5+</li>
                                        <li><i class="ti-location-pin"></i> Bolivia</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="item">
                        <div class="position-re o-hidden"><img src="img/tours/tourportada5.jpg" alt=""></div> <span class="category"><a href="#0">Nuevo</a></span>
                        <div class="con">
                            <h5><a href="tour-aves.html">Observación de aves</a></h5>
                            <div class="line"></div>
                            <div class="row facilities">
                                <div class="col col-md-12">
                                    <ul>
                                        <li><i class="ti-time"></i> 5 Días </li>
                                        <li><i class="ti-user"></i> 5+</li>
                                        <li><i class="ti-location-pin"></i> Parque Nacional del Manu </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- sostenibilidad -->
<section class="sostenibilidad section-padding">
    <div class="container">
        <div class="row align-items-center">
            <!-- Imagen al lado izquierdo -->
            <div class="col-md-4 text-center">
                <img src="img/sostenibilidad-azul.png" alt="Sostenibilidad" class="sostenibilidad-img" style="max-height: 150px; width: auto; object-fit: contain; opacity: 0.9;">
            </div>
            <!-- Texto centrado -->
            <div class="col-md-8">
                <p style="text-align: justify; font-size: 16px; line-height: 1.8;">
                    En Nombre del Sector Yanico, guardaparques locales de la Reserva Nacional del Titicaca y de la agencia de Viajes Titicaca Experiences, creemos en un turismo responsable que protege nuestro entorno y fortalece las comunidades locales. Nos comprometemos a minimizar nuestro impacto ambiental, promover prácticas sostenibles y apoyar iniciativas que generen bienestar para las futuras generaciones. A través de la educación, la conservación y el respeto por la biodiversidad, buscamos que cada experiencia de viaje contribuya a la regeneración de nuestros ecosistemas y al desarrollo cultural de los territorios que exploramos.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- fin sostenibilidad -->

    <!-- Numbers -->
    <section class="numbers">
        <div class="section-padding bg-img bg-fixed back-position-center" data-background="img/slider/home-balsa.jpg" data-overlay-dark="6">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="item text-center"> 
                             
                            <span class="icon">
                                <i class="front flaticon-air-freight"></i>
                                <i class="back flaticon-air-freight"></i>
                            </span>
                            <!-- <h3 class="count">+30</h3>-->
                            <h5>Tours<br>especiales</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="item text-center"> 
                             
                            <span class="icon">
                                <i class="font flaticon-house"></i>
                                <i class="back flaticon-house"></i>
                            </span>
                            <!--<h3 class="count">2</h3> -->
                            <h5>Tours de <br>Aventura</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="item text-center"> 
                             
                            <span class="icon">
                                <i class="front flaticon-ship"></i>
                                <i class="back flaticon-ship"></i>
                            </span>
                            <!--<h3 class="count">100</h3>-->
                            <h5>Tours <br>místicos</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="item text-center"> 
                            <span class="icon">
                                <i class="front flaticon-tag"></i>
                                <i class="back flaticon-tag"></i>
                            </span>
                            <!--<h3 class="count">100</h3> -->
                            <h5>Observación <br> de aves</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Destination 1 -->
    <!--
    <section class="destination1 section-padding bg-lightnav">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-subtitle">Top Destination</div>
                    <div class="section-title">Popular <span>Destination</span></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="owl-carousel owl-theme">
                        <div class="item">
                            <div class="position-re o-hidden"><img src="img/destination/maldives1.jpg" alt=""></div> 
                            <span class="category"><a href="#0">New</a></span>
                            <div class="con"> 
                                <h5><a href="tour-details.html"><i class="ti-location-pin"></i> Maldives</a></h5>
                                <div class="line"></div>
                                <div class="row facilities">
                                    <div class="col col-md-8">
                                        <p>4 Tour Packages</p>
                                    </div>
                                    <div class="col col-md-4 text-right">
                                        <div class="permalink"><a href="tour-details.html">Explore <i class="ti-arrow-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="position-re o-hidden"> <img src="img/destination/canada1.jpg" alt=""> </div> 
                            <span class="category"><a href="#0">25% OFF</a></span>
                            <div class="con"> 
                                <h5><a href="tour-details.html"><i class="ti-location-pin"></i> Canada</a></h5>
                                <div class="line"></div>
                                <div class="row facilities">
                                    <div class="col col-md-8">
                                        <p>3 Tour Packages</p>
                                    </div>
                                    <div class="col col-md-4 text-right">
                                        <div class="permalink"><a href="tour-details.html">Explore <i class="ti-arrow-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="position-re o-hidden"><img src="img/destination/italy1.jpg" alt=""></div> 
                            <span class="category"><a href="#0">7 Tours</a></span>
                            <div class="con">
                                <h5><a href="#0"><i class="ti-location-pin"></i> Italy</a></h5>
                                <div class="line"></div>
                                <div class="row facilities">
                                    <div class="col col-md-8">
                                        <p>7 Tour Packages</p>
                                    </div>
                                    <div class="col col-md-4 text-right">
                                        <div class="permalink"><a href="tour-details.html">Explore <i class="ti-arrow-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="position-re o-hidden"><img src="img/destination/greece1.jpg" alt=""></div> 
                            <span class="category"><a href="#0">New</a></span>
                            <div class="con">
                                <h5><a href="#0"><i class="ti-location-pin"></i> Greece</a></h5>
                                <div class="line"></div>
                                <div class="row facilities">
                                    <div class="col col-md-8">
                                        <p>6 Tour Packages</p>
                                    </div>
                                    <div class="col col-md-4 text-right">
                                        <div class="permalink"><a href="tour-details.html">Explore <i class="ti-arrow-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="position-re o-hidden"><img src="img/destination/france1.jpg" alt=""></div> 
                            <span class="category"><a href="#0">15% OFF</a></span>
                            <div class="con">
                                <h5><a href="#0"><i class="ti-location-pin"></i> France</a></h5>
                                <div class="line"></div>
                                <div class="row facilities">
                                    <div class="col col-md-8">
                                        <p>10 Tour Packages</p>
                                    </div>
                                    <div class="col col-md-4 text-right">
                                        <div class="permalink"><a href="tour-details.html">Explore <i class="ti-arrow-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="position-re o-hidden"><img src="img/destination/dubai1.jpg" alt=""></div> 
                            <span class="category"><a href="tours2.html">7 Tours</a></span>
                            <div class="con">
                                <h5><a href="tour-details.html"><i class="ti-location-pin"></i> Dubai</a></h5>
                                <div class="line"></div>
                                <div class="row facilities">
                                    <div class="col col-md-8">
                                        <p>7 Tour Packages</p>
                                    </div>
                                    <div class="col col-md-4 text-right">
                                        <div class="permalink"><a href="tour-details.html">Explore <i class="ti-arrow-right"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
-->
    <!-- Banner Tour Video -->
    <!--
    <section class="dynamic-video-wrapper" data-overlay-dark="3">
        <video width="100%" height="100%" autoplay="autoplay" muted preload="auto" loop="loop">
            <source src="video/travel-lago.mp4" type="video/mp4">
        </video>
        <div class="wrap-content v-middle">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        
                        <h1>Tour </h1>
                        <h4><i class="ti-location-pin"></i> Ubicaciòn &nbsp;&nbsp; <i class="ti-timer"></i> 4 dìas + 3 Noches</h4>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    -->
    <!-- Italy & France Tours 1 -->
 
    <!-- Blog -->
    <section class="blog section-padding bg-navy">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-subtitle"><span>Travel Blog</span></div>
                    <div class="section-title"><span>Experiencias de</span> viaje</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="owl-carousel owl-theme">
                        <?php foreach ($recent_home_posts as $p): 
                            $date_parts = get_spanish_date_parts($p['date'] ?? '');
                            $thumb = !empty($p['image_thumb']) ? $p['image_thumb'] : (!empty($p['image_cover']) ? $p['image_cover'] : 'img/slider/slidepost.jpg');
                            $p_url = 'post.php?slug=' . urlencode($p['slug']);
                            $cat_url = 'blog.php?cat=' . urlencode($p['category'] ?? '');
                        ?>
                            <div class="item">
                                <div class="position-re o-hidden"> 
                                    <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                                    <div class="date">
                                        <a href="<?= $p_url ?>"> 
                                            <span><?= htmlspecialchars($date_parts['month_short']) ?></span> 
                                            <i><?= htmlspecialchars($date_parts['day']) ?></i> 
                                        </a>
                                    </div>
                                </div>
                                <div class="con"> 
                                    <span class="category">
                                        <a href="<?= $cat_url ?>"><?= htmlspecialchars($p['category'] ?? 'Experiencia') ?></a>
                                    </span>
                                    <h5><a href="<?= $p_url ?>"><?= htmlspecialchars($p['title']) ?></a></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
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
                        <!--<p><i class="ti-check"></i><small>Call us, it's toll-free.</small></p> -->
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
    <?php include __DIR__ . "/includes/footer.php"; ?>

    <!-- jQuery -->
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
</body>


</html>
