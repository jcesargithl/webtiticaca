<?php
// admin/backup.php - Generador y descargador de copias de seguridad de datos y fotos
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

require_admin_login();

// Desactivar buffer de salida previo para enviar el archivo limpio
while (ob_get_level()) {
    ob_end_clean();
}

$date_str = date('Y-m-d_His');
$base_dir = dirname(__DIR__);

// Verificar si ZipArchive está disponible en el servidor (estándar en cPanel)
if (class_exists('ZipArchive')) {
    $zip_filename = 'copia_seguridad_titicaca_' . $date_str . '.zip';
    $tmp_file = tempnam(sys_get_temp_dir(), 'titicaca_backup_');

    $zip = new ZipArchive();
    if ($zip->open($tmp_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        
        // 1. Respaldar archivos JSON de la base de datos plana
        $data_dir = $base_dir . '/data';
        if (is_dir($data_dir)) {
            $files = glob($data_dir . '/*.json');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $zip->addFile($file, 'data/' . basename($file));
                }
            }
        }

        // 2. Respaldar fotos subidas de la Galería
        $gal_upload_dir = $base_dir . '/img/slider/galeria/uploads';
        if (is_dir($gal_upload_dir)) {
            $files = scandir($gal_upload_dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($gal_upload_dir . '/' . $file)) {
                    $zip->addFile($gal_upload_dir . '/' . $file, 'uploads/galeria/' . $file);
                }
            }
        }

        // 3. Respaldar fotos subidas del Blog
        $blog_upload_dir = $base_dir . '/img/blog/uploads';
        if (is_dir($blog_upload_dir)) {
            $files = scandir($blog_upload_dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($blog_upload_dir . '/' . $file)) {
                    $zip->addFile($blog_upload_dir . '/' . $file, 'uploads/blog/' . $file);
                }
            }
        }

        // 4. Respaldar avatares subidos del equipo / reseñas
        $team_upload_dir = $base_dir . '/img/team/uploads';
        if (is_dir($team_upload_dir)) {
            $files = scandir($team_upload_dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($team_upload_dir . '/' . $file)) {
                    $zip->addFile($team_upload_dir . '/' . $file, 'uploads/team/' . $file);
                }
            }
        }

        // 5. Agregar archivo informativo LEEME
        $readme_content = "==============================================================\n" .
                          "COPIA DE SEGURIDAD - TITICACA EXPERIENCES PERU\n" .
                          "==============================================================\n" .
                          "Fecha de generacion: " . date('Y-m-d H:i:s') . "\n\n" .
                          "Contenido de este respaldo:\n" .
                          "- data/reviews.json    : Resenas y testimonios de viajeros\n" .
                          "- data/posts.json      : Articulos publicados en el blog\n" .
                          "- data/gallery.json    : Catalogo de fotos de la galeria\n" .
                          "- data/videos.json     : Videos de YouTube de la galeria\n" .
                          "- data/messages.json   : Bandeja de mensajes de contacto y reservas\n" .
                          "- uploads/galeria/     : Fotos personalizadas de galeria\n" .
                          "- uploads/blog/        : Fotos de portada de articulos del blog\n" .
                          "- uploads/team/        : Avatares subidos de viajeros y equipo\n\n" .
                          "Instrucciones de restauracion:\n" .
                          "1. Copia los archivos de 'data/' en la carpeta 'public_html/data/' de tu cPanel.\n" .
                          "2. Copia los archivos de 'uploads/' en sus respectivas carpetas dentro de 'public_html/img/'.\n" .
                          "==============================================================\n";
        $zip->addFromString('LEEME_RESPALDO.txt', $readme_content);

        $zip->close();

        // Enviar ZIP al navegador para descarga
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
        header('Content-Length: ' . filesize($tmp_file));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($tmp_file);
        @unlink($tmp_file);
        exit;
    }
}

// Fallback: Si ZipArchive no estuviera activo, descargar un consolidado JSON completo
$bundle = [
    'generated_at' => date('Y-m-d H:i:s'),
    'site' => 'Titicaca Experiences Peru',
    'reviews' => json_decode(@file_get_contents($base_dir . '/data/reviews.json') ?: '[]', true),
    'posts' => json_decode(@file_get_contents($base_dir . '/data/posts.json') ?: '[]', true),
    'gallery' => json_decode(@file_get_contents($base_dir . '/data/gallery.json') ?: '[]', true),
    'videos' => json_decode(@file_get_contents($base_dir . '/data/videos.json') ?: '[]', true),
    'messages' => json_decode(@file_get_contents($base_dir . '/data/messages.json') ?: '[]', true)
];

$json_filename = 'copia_seguridad_datos_titicaca_' . $date_str . '.json';
$output = json_encode($bundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $json_filename . '"');
header('Content-Length: ' . strlen($output));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $output;
exit;
