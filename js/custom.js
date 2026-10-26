/* -------------------------------------------------------

[ Custom settings ]

01. ScrollIt
02. Navbar scrolling background
03. Close navbar-collapse when a  clicked
04. Sections background image from data background 
05. Animations
06. YouTubePopUp
07. CountUp Numbers
08. Testimonials owlCarousel
09. Tours 1 owlCarousel
10. Tours 2 owlCarousel
11. Tours 3 owlCarousel
12. Destination 1 owlCarousel
13. Tour Page owlCarousel
14. Blog owlCarousel
15. Team owlCarousel
16. Clients owlCarousel
17. Accordion Box (for Faqs)
18. MagnificPopup Gallery
19. Smooth Scrolling
20. Scroll back to top
21. Select2
22. Datapicker
23. Slider owlCarousel - (Inner Page Slider)
24. Slider owlCarousel (Homepage Slider)
25. Preloader
26. Contact Form

------------------------------------------------------- */
$(function () {
    "use strict";
    var wind = $(window);
    
    // ScrollIt
    $.scrollIt({
        upKey: 38, // key code to navigate to the next section
        downKey: 40, // key code to navigate to the previous section
        easing: 'swing', // the easing function for animation
        scrollTime: 600, // how long (in ms) the animation takes
        activeClass: 'active', // class given to the active nav element
        onPageChange: null, // function(pageIndex) that is called when page is changed
        topOffset: -70 // offste (in px) for fixed top navigation
    });
    
    // Navbar scrolling background
    function checkNavbarScroll() {
        var bodyScroll = wind.scrollTop(),
            navbar = $(".navbar"),
            logo = $(".navbar .logo> img");
        if (bodyScroll > 100) {
            navbar.addClass("nav-scroll");
            if (logo.attr('src') !== 'img/logo-dark.png') {
                logo.attr('src', 'img/logo-dark.png');
            }
        } else {
            navbar.removeClass("nav-scroll");
            if (logo.attr('src') !== 'img/logo-light.png') {
                logo.attr('src', 'img/logo-light.png');
            }
        }
    }
    wind.on("scroll", checkNavbarScroll);
    checkNavbarScroll();
    
    // Close navbar-collapse when a  clicked
    $(".navbar-nav .dropdown-item a").on('click', function () {
        $(".navbar-collapse").removeClass("show");
    });
    
     $(".navbar-nav .dropdown-item a").on('click', function () {
        $(".navbar-collapse").removeClass("show");
    });
    
    
    
    
    // Sections background image from data background
    var pageSection = $(".bg-img, section");
    pageSection.each(function (indx) {
        if ($(this).attr("data-background")) {
            $(this).css("background-image", "url(" + $(this).data("background") + ")");
        }
    });

    
    // Animations
    var contentWayPoint = function () {
        var i = 0;
        $('.animate-box').waypoint(function (direction) {
            if (direction === 'down' && !$(this.element).hasClass('animated')) {
                i++;
                $(this.element).addClass('item-animate');
                setTimeout(function () {
                    $('body .animate-box.item-animate').each(function (k) {
                        var el = $(this);
                        setTimeout(function () {
                            var effect = el.data('animate-effect');
                            if (effect === 'fadeIn') {
                                el.addClass('fadeIn animated');
                            } else if (effect === 'fadeInLeft') {
                                el.addClass('fadeInLeft animated');
                            } else if (effect === 'fadeInRight') {
                                el.addClass('fadeInRight animated');
                            } else {
                                el.addClass('fadeInUp animated');
                            }
                            el.removeClass('item-animate');
                        }, k * 200, 'easeInOutExpo');
                    });
                }, 100);
            }
        }, {
            offset: '85%'
        });
    };
    $(function () {
        contentWayPoint();
    });
    
    
    // YouTubePopUp
    $("a.vid").YouTubePopUp();
    
     // CountUp Numbers
    $('.numbers .count').countUp({
        delay: 10,
        time: 1500
    });
    
    // Testimonials owlCarousel *
    var $testiCarousel = $('.testimonials .owl-carousel');
    function initTestimonialsCarousel($el) {
        $el.owlCarousel({
            loop: true,
            margin: 30,
            mouseDrag: true,
            autoplay: false,
            dots: true,
            nav: false,
            navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        });
    }

    if ($testiCarousel.length) {
        if ($testiCarousel.attr('data-server-rendered') === 'true') {
            initTestimonialsCarousel($testiCarousel);
        } else {
            // Cargar reseñas dinámicas aleatorias vía API para páginas estáticas
            var apiReviewsUrl = (window.location.pathname.indexOf('/post/') !== -1 ? '../' : '') + 'api/reviews.php?limit=3';
            $.getJSON(apiReviewsUrl, function(data) {
                if (data && data.success && data.reviews && data.reviews.length > 0) {
                    var itemsHtml = '';
                    var googleIconSvg = '<svg width="12" height="12" viewBox="0 0 24 24" style="vertical-align: -1px; margin-right: 3px;"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.67v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.16z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>';
                    var tripadvisorIconSvg = '<svg width="14" height="14" viewBox="0 0 713.5 713.5" style="vertical-align: -2px; margin-right: 3px;"><circle fill="#34E0A1" cx="356.75" cy="356.75" r="356.75"/><path fill="#000000" d="M577.095,287.152l43.049-46.836h-95.465c-47.792-32.646-105.51-51.659-167.931-51.659 c-62.342,0-119.899,19.054-167.612,51.659H93.432l43.049,46.836c-26.387,24.075-42.929,58.754-42.929,97.259 c0,72.665,58.914,131.578,131.579,131.578c34.519,0,65.968-13.313,89.446-35.077l42.172,45.919l42.172-45.879 c23.478,21.764,54.887,35.037,89.406,35.037c72.665,0,131.658-58.913,131.658-131.578 C620.024,345.866,603.483,311.188,577.095,287.152z M225.17,473.458c-49.188,0-89.047-39.859-89.047-89.047 s39.86-89.048,89.047-89.048c49.187,0,89.047,39.86,89.047,89.048S274.357,473.458,225.17,473.458z M356.788,381.82 c0-58.595-42.61-108.898-98.853-130.383c30.413-12.716,63.776-19.771,98.813-19.771s68.439,7.055,98.853,19.771 C399.399,272.962,356.788,323.226,356.788,381.82z M488.367,473.458c-49.188,0-89.048-39.859-89.048-89.047 s39.86-89.048,89.048-89.048s89.047,39.86,89.047,89.048S537.554,473.458,488.367,473.458z M488.367,337.694 c-25.79,0-46.677,20.887-46.677,46.677c0,25.789,20.887,46.676,46.677,46.676c25.789,0,46.676-20.887,46.676-46.676 C535.042,358.621,514.156,337.694,488.367,337.694z M271.846,384.411c0,25.789-20.887,46.676-46.676,46.676 s-46.676-20.887-46.676-46.676c0-25.79,20.887-46.677,46.676-46.677C250.959,337.694,271.846,358.621,271.846,384.411z"/></svg>';

                    $.each(data.reviews, function(idx, rev) {
                        var name = rev.name || 'Viajero';
                        var role = rev.role || 'Viajero';
                        var text = rev.text || '';
                        var photo = rev.photo || 'img/team/07.png';
                        var rating = rev.rating || 5;
                        var reviewUrl = (rev.review_url || rev.google_url || '').trim();
                        var hasLink = reviewUrl.length > 0;
                        var badgeHtml = '';
                        var linkTitle = 'Ver reseña original';

                        if (hasLink) {
                            var lowerUrl = reviewUrl.toLowerCase();
                            if (lowerUrl.indexOf('tripadvisor') !== -1) {
                                badgeHtml = '<span class="tripadvisor-badge-pill" title="Reseña verificada en Tripadvisor">' + tripadvisorIconSvg + 'Tripadvisor</span>';
                                linkTitle = 'Ver reseña en Tripadvisor';
                            } else if (lowerUrl.indexOf('google') !== -1 || lowerUrl.indexOf('goo.gl') !== -1) {
                                badgeHtml = '<span class="google-badge-pill" title="Reseña en Google">' + googleIconSvg + 'Google</span>';
                                linkTitle = 'Ver reseña en Google';
                            } else {
                                var linkIconSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -1px; margin-right: 3px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>';
                                badgeHtml = '<span class="review-badge-pill" title="Ver reseña original">' + linkIconSvg + 'Reseña</span>';
                                linkTitle = 'Ver reseña original';
                            }
                        }

                        var starsHtml = '<div class="rating">';
                        for (var s = 1; s <= 5; s++) {
                            starsHtml += ' <i class="star ' + (s <= rating ? 'active' : '') + '"></i>';
                        }
                        starsHtml += ' </div>';

                        itemsHtml += '<div class="item">';
                        if (hasLink) {
                            itemsHtml += '<a href="' + reviewUrl + '" target="_blank" rel="noopener noreferrer" class="testimonial-card-link" title="' + linkTitle + '">';
                        }
                        itemsHtml += '<p>' + text + '</p>';
                        itemsHtml += '<div class="info">';
                        itemsHtml += '<div class="author-img"> <img src="' + photo + '" alt="' + name + '"> </div>';
                        itemsHtml += '<div class="cont">';
                        itemsHtml += starsHtml;
                        if (hasLink) {
                            itemsHtml += '<h6>' + name + ' ' + badgeHtml + '</h6> <span>' + role + '</span>';
                        } else {
                            itemsHtml += '<h6>' + name + '</h6> <span>' + role + '</span>';
                        }
                        itemsHtml += '</div></div>';
                        if (hasLink) {
                            itemsHtml += '</a>';
                        }
                        itemsHtml += '</div>';
                    });
                    $testiCarousel.html(itemsHtml);
                }
                initTestimonialsCarousel($testiCarousel);
            }).fail(function() {
                initTestimonialsCarousel($testiCarousel);
            });
        }
    }
    
    // Tours 1 owlCarousel *
    $('.tours1 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 2
            }
        }
    });
    
    // Tours 2 owlCarousel *
    $('.tours2 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    });
    
    // Tours 3 owlCarousel *
    $('.tours3 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Destination 1 owlCarousel *
    $('.destination1 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Tour Page owlCarousel *
    $('.tour-page .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: true,
        dots: false,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    });

    // Blog owlCarousel *
    $('.blog .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: false,
        nav: true,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                dots: true,
                nav: false
            },
            600: {
                items: 2,
                dots: true,
                nav: false
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Team owlCarousel *
    $('.team .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        dots: true,
        mouseDrag: true,
        autoplay: false,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                dots: true
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Clients owlCarousel *
    $('.clients .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: true,
        dots: false,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>","<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                margin: 10,
                items: 3
            },
            600: {
                items: 3
            },
            1000: {
                items: 4
            }
        }
    });

    // Footer Trust Carousel (de 3 en 3)
    if ($('.footer-trust-carousel').length) {
        $('.footer-trust-carousel').owlCarousel({
            loop: true,
            margin: 15,
            items: 3,
            slideBy: 3,
            mouseDrag: true,
            touchDrag: true,
            autoplay: true,
            autoplayTimeout: 3500,
            autoplayHoverPause: true,
            smartSpeed: 500,
            dots: false,
            nav: false,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 3,
                    slideBy: 3,
                    margin: 10
                },
                600: {
                    items: 3,
                    slideBy: 3,
                    margin: 12
                },
                1000: {
                    items: 3,
                    slideBy: 3,
                    margin: 15
                }
            }
        });
    }
    
    // Accordion Box (for Faqs)
    if ($(".accordion-box").length) {
        $(".accordion-box").on("click", ".acc-btn", function () {
            var outerBox = $(this).parents(".accordion-box");
            var target = $(this).parents(".accordion");
            if ($(this).next(".acc-content").is(":visible")) {
                //return false;
                $(this).removeClass("active");
                $(this).next(".acc-content").slideUp(300);
                $(outerBox).children(".accordion").removeClass("active-block");
            } else {
                $(outerBox).find(".accordion .acc-btn").removeClass("active");
                $(this).addClass("active");
                $(outerBox).children(".accordion").removeClass("active-block");
                $(outerBox).find(".accordion").children(".acc-content").slideUp(300);
                target.addClass("active-block");
                $(this).next(".acc-content").slideDown(300);
            }
        });
    }
    
    // MagnificPopup Gallery
    $('.gallery').magnificPopup({
        delegate: '.popimg',
        type: 'image',
        gallery: {
            enabled: true
        }
    });
    $(".img-zoom").magnificPopup({
        type: "image",
        closeOnContentClick: !0,
        mainClass: "mfp-fade",
        gallery: {
            enabled: !0,
            navigateByImgClick: !0,
            preload: [0, 1]
        }
    })
    $('.magnific-youtube, .magnific-vimeo, .magnific-custom').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 300,
        preloader: false,
        fixedContentPos: false
    });
    
    // Smooth Scrolling
    $('a[href*="#"]')
    // Remove links that don't actually link to anything
    .not('[href="#"]').not('[href="#0"]').click(function (event) {
        // On-page links
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            // Figure out element to scroll to
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            // Does a scroll target exist?
            if (target.length) {
                // Only prevent default if animation is actually gonna happen
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 1000, function () {
                    // Callback after animation
                    // Must change focus!
                    var $target = $(target);
                    $target.focus();
                    if ($target.is(":focus")) { // Checking if the target was focused
                        return false;
                    } else {
                        $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
                        $target.focus(); // Set focus again
                    };
                });
            }
        }
    });
    
    //  Scroll back to top
    var progressPath = document.querySelector('.progress-wrap path');
    var pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
    progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
    var updateProgress = function () {
        var scroll = $(window).scrollTop();
        var height = $(document).height() - $(window).height();
        var progress = pathLength - (scroll * pathLength / height);
        progressPath.style.strokeDashoffset = progress;
    }
    updateProgress();
    $(window).scroll(updateProgress);
    var offset = 150;
    var duration = 550;
    jQuery(window).on('scroll', function () {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.progress-wrap').addClass('active-progress');
        } else {
            jQuery('.progress-wrap').removeClass('active-progress');
        }
    });
    jQuery('.progress-wrap').on('click', function (event) {
        event.preventDefault();
        jQuery('html, body').animate({
            scrollTop: 0
        }, duration);
        return false;
    })
    
    // Select2
    $('.select2').select2({
        minimumResultsForSearch: Infinity,
    });
    
    // Datapicker
    $(".datepicker").datepicker({
        orientation: "top"
    });
     
});


// Slider  
$(document).ready(function () {
    var owl = $('.header .owl-carousel');
    
    // Slider owlCarousel - (Inner Page Slider)
    $('.slider .owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        dots: true,
        margin: 0,
        autoplay: true,
        autoplayTimeout: 5000,
        nav: false,
        navText: ['<i class="ti-angle-left" aria-hidden="true"></i>', '<i class="ti-angle-right" aria-hidden="true"></i>'],
        responsiveClass: true,
        responsive: {
            0: {
                dots: true
            },
            600: {
                dots: true
            },
            1000: {
                dots: true
            }
        }
    });
    
    // Slider owlCarousel (Homepage Slider)
    $('.slider-fade .owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        dots: true,
        margin: 0,
        autoplay: true,
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        nav: false,
        navText: ['<i class="ti-angle-left" aria-hidden="true"></i>', '<i class="ti-angle-right" aria-hidden="true"></i>'],
        responsiveClass: true,
        responsive: {
            0: {
                dots: false
            },
            600: {
                dots: false
            },
            1000: {
                dots: true
            }
        }
    });
    owl.on('changed.owl.carousel', function (event) {
        var item = event.item.index - 2; // Position of the current item
        $('span').removeClass('animated fadeInUp');
        $('h4').removeClass('animated fadeInUp');
        $('h1').removeClass('animated fadeInUp');
        $('p').removeClass('animated fadeInUp');
        $('.butn-light').removeClass('animated fadeInUp');
        $('.butn-dark').removeClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('span').addClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('h4').addClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('h1').addClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('p').addClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('.butn-light').addClass('animated fadeInUp');
        $('.owl-item').not('.cloned').eq(item).find('.butn-dark').addClass('animated fadeInUp');
    });
});


// Preloader
$("#preloader").fadeOut(700);
	$(".preloader-bg").delay(700).fadeOut(700);
	var wind = $(window);


// Contact Form
var form = $('.contact__form'),
    message = $('.contact__msg'),
    form_data;
    // success function
    function done_func(response) {
        message.fadeIn().removeClass('alert-danger').addClass('alert-success');
        message.text(response);
        setTimeout(function () {
            message.fadeOut();
        }, 4000);
        form.find('input:not([type="submit"]):not([type="checkbox"]), textarea').val('');
        form.find('input[type="checkbox"]').prop('checked', false);
        if (typeof turnstile !== 'undefined') {
            try { turnstile.reset(); } catch (err) {}
        }
    }
    // fail function
    function fail_func(data) {
        message.fadeIn().removeClass('alert-success').addClass('alert-danger');
        var errorMsg = data.responseText || 'Ocurrió un error al procesar tu solicitud. Intenta nuevamente.';
        message.text(errorMsg);
        setTimeout(function () {
            message.fadeOut();
        }, 5000);
        if (typeof turnstile !== 'undefined') {
            try { turnstile.reset(); } catch (err) {}
        }
    }
    form.submit(function (e) {
        e.preventDefault();
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        form_data = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form_data
        }).done(done_func).fail(fail_func).always(function () {
            submitBtn.prop('disabled', false);
        });
    });

// About Section - Dynamic Tour Slideshow (Ken Burns + Crossfade)
$(function () {
    var $faders = $('.about-fader');
    if (!$faders.length) return;

    var tourSlides = [
        { img: 'img/about2.jpg', link: 'tour-canoas.html', title: 'Canoas Polinésicas' },
        { img: 'img/slider/touramantani/slide1.jpg', link: 'tour-amantani.html', title: 'Isla Amantaní' },
        { img: 'img/slider/toururos/max5.jpg', link: 'tour-uros.html', title: 'Islas de los Uros' },
        { img: 'img/slider/tourtaquile/slide1.jpg', link: 'tour-taquile.html', title: 'Isla Taquile' },
        { img: 'img/slider/toursillustani/max4.jpg', link: 'tour-sillustani.html', title: 'Chullpas de Sillustani' },
        { img: 'img/tours/touravespuno/AAG_1879_max.jpg', link: 'tour-aves-puno.html', title: 'Observación de Aves' },
        { img: 'img/slider/touraymara/max1.jpg', link: 'tour-ruta-aymara.html', title: 'Ruta Aymara' },
        { img: 'img/slider/tourquechua/slide.jpg', link: 'tour-ruta-quechua.html', title: 'Ruta Quechua' },
        { img: 'img/tours/tourcusco/max1.jpg', link: 'tour-cusco.html', title: 'Paquete Cusco' },
        { img: 'img/tours/tourbolivia/max3.jpg', link: 'tour-bolivia.html', title: 'Paquete Bolivia' }
    ];

    // Preload images into memory for smooth playback
    tourSlides.forEach(function (tour) {
        var preloader = new Image();
        preloader.src = tour.img;
    });

    // Fisher-Yates shuffle
    function shuffleArray(arr) {
        var copy = arr.slice();
        for (var i = copy.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var temp = copy[i];
            copy[i] = copy[j];
            copy[j] = temp;
        }
        return copy;
    }

    $faders.each(function () {
        var $fader = $(this);
        var $link = $fader.closest('.about-tour-link');
        var $badge = $fader.find('.about-tour-badge');
        var $title = $fader.find('.about-tour-title');
        var $slide1 = $fader.find('.about-slide-1');
        var $slide2 = $fader.find('.about-slide-2');

        var playlist = shuffleArray(tourSlides);
        // Avoid repeating Canoas immediately if about2.jpg is already active
        if (playlist.length > 1 && playlist[0].img.indexOf('about2.jpg') !== -1) {
            playlist.push(playlist.shift());
        }

        var currentIndex = 0;
        var isPaused = false;
        var isTransitioning = false;
        var intervalMs = 5500;
        var activeIsSlide1 = true;

        // Ensure initial slide is active
        $slide1.addClass('active');

        function showNextTour() {
            if (isPaused || isTransitioning) return;

            var nextTour = playlist[currentIndex];
            currentIndex = (currentIndex + 1) % playlist.length;
            if (currentIndex === 0) {
                playlist = shuffleArray(tourSlides);
            }

            isTransitioning = true;

            // Guarantee image is fully loaded in memory BEFORE starting transition
            var loader = new Image();
            var hasTriggered = false;

            function triggerSlideTransition() {
                if (hasTriggered) return;
                hasTriggered = true;

                var $front = activeIsSlide1 ? $slide1 : $slide2;
                var $back = activeIsSlide1 ? $slide2 : $slide1;

                // Set image on the back slide
                $back.attr('src', nextTour.img).attr('alt', nextTour.title);

                // Fade badge slightly during sync
                $badge.css({ 'opacity': 0, 'transform': 'translateY(4px)' });

                setTimeout(function () {
                    $link.attr('href', nextTour.link);
                    $title.text(nextTour.title);
                    $badge.css({ 'opacity': 1, 'transform': 'translateY(0)' });

                    $back.css('z-index', 2).addClass('active');

                    setTimeout(function () {
                        $front.removeClass('active').css('z-index', 1);
                        activeIsSlide1 = !activeIsSlide1;
                        isTransitioning = false;
                    }, 1450);
                }, 80);
            }

            loader.onload = triggerSlideTransition;
            loader.onerror = function () {
                isTransitioning = false;
            };
            loader.src = nextTour.img;
            if (loader.complete) {
                triggerSlideTransition();
            }
        }

        var timer = setInterval(showNextTour, intervalMs);

        // Pause on hover so visitor can admire photo and click
        $link.on('mouseenter', function () {
            isPaused = true;
        }).on('mouseleave', function () {
            isPaused = false;
        });
    });
});
