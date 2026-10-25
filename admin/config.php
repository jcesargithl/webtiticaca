<?php
// admin/config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración privada local si existe (ignorada por Git)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Hash criptográfico de la contraseña (SHA-256). Nunca se guarda en texto plano en GitHub.
if (!defined('ADMIN_PASSWORD_HASH')) {
    // Hash de la contraseña inicial 'titicaca2026'
    define('ADMIN_PASSWORD_HASH', 'e095c7a92148a1797fe5aac390763ff6ed0013aba12c83f927043cbea593b702');
}

// Nombre del sitio en el panel
define('SITE_NAME', 'Titicaca Experiences - Admin');
