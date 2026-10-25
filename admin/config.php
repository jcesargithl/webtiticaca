<?php
// admin/config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Contraseña de acceso al panel (cámbiala cuando desees)
define('ADMIN_PASSWORD', 'titicaca2026');

// Nombre del sitio en el panel
define('SITE_NAME', 'Titicaca Experiences - Admin');
