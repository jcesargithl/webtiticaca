<?php
// includes/navbar.php - Menú de navegación principal con selector de idiomas
$current_script = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-md">
    <div class="container">
        <!-- Logo -->
        <div class="logo-wrapper">
            <a class="logo" href="index.html"> <img src="img/logo-titie.png" class="logo-img" alt="Titicaca Experiences"> </a>
        </div>
        <!-- Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"><i class="ti-menu"></i></span> </button>
        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= in_array($current_script, ['index.html', 'index.php', '']) ? 'active' : '' ?>" href="index.html">Inicio</a></li>

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
                                        <li><a href="tour-taquile-day.html" class="dropdown-item"><span>Taquile (Full Day)</span></a></li>
                                        <li><a href="tour-taquile.html" class="dropdown-item"><span>Taquile (2 días)</span></a></li>
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

                <li class="nav-item"><a class="nav-link <?= $current_script === 'about.html' ? 'active' : '' ?>" href="about.html">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link <?= in_array($current_script, ['galeria.html', 'galeria.php']) ? 'active' : '' ?>" href="galeria.html">Galería</a></li>
                <li class="nav-item <?= in_array($current_script, ['blog.php', 'post.php', 'blog.html']) ? 'active' : '' ?>"><a class="nav-link <?= in_array($current_script, ['blog.php', 'post.php', 'blog.html']) ? 'active' : '' ?>" href="blog.php">Blog</a></li>
                <li class="nav-item"><a class="nav-link <?= $current_script === 'contacto.html' ? 'active' : '' ?>" href="contacto.html">Contacto</a></li>
            </ul>
        </div>

        <!-- Selector de idioma (Estilo Appstorrent.ru) -->
        <div class="lang guest-language" id="lang_min">
            <i class="guest-language-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
            </i>
            <span class="langHeader" id="lang">Es</span>
            <span class="lang-arrow"></span>
            <div class="dropdown-lang-content" id="langDropdown">
                <a role="button" data-google-lang="es">Español</a>
                <a role="button" data-google-lang="en">English</a>
                <a role="button" data-google-lang="fr">Français</a>
                <a role="button" data-google-lang="de">Deutsch</a>
                <a role="button" data-google-lang="pt">Português</a>
                <a role="button" data-google-lang="it">Italiano</a>
                <a role="button" data-google-lang="zh-CN">中文</a>
            </div>
        </div>
    </div>
</nav>
