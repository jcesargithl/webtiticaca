<?php
// includes/testimonials.php - Componente modular de testimonios con reseñas dinámicas aleatorias
require_once __DIR__ . '/reviews_helpers.php';
?>
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
                <!-- Testimonials Box -->
                <div class="col-md-5 offset-md-2">
                    <div class="testimonials-box">
                        <div class="head-box">
                            <h6>Testimonios</h6>
                            <h4>Reseñas de nuestros viajeros</h4>
                        </div>
                        <div class="owl-carousel owl-theme" id="dynamic-testimonials-carousel" data-server-rendered="true">
<?= render_testimonials_carousel_items(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
