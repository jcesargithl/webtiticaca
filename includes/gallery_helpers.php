<?php
// includes/gallery_helpers.php

define('GALLERY_FILE', __DIR__ . '/../data/gallery.json');
define('VIDEOS_FILE', __DIR__ . '/../data/videos.json');

/**
 * Obtener todas las imágenes de la galería
 */
function get_gallery_images() {
    if (!file_exists(GALLERY_FILE)) {
        return [];
    }
    $json = file_get_contents(GALLERY_FILE);
    $images = json_decode($json, true);
    return is_array($images) ? $images : [];
}

/**
 * Guardar la lista de imágenes de la galería
 */
function save_gallery_images($images) {
    $dir = dirname(GALLERY_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $json = json_encode(array_values($images), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $bytes = file_put_contents(GALLERY_FILE, $json, LOCK_EX);
    if ($bytes !== false) {
        @chmod(GALLERY_FILE, 0666);
        clearstatcache(true, GALLERY_FILE);
        return true;
    }
    return false;
}

/**
 * Obtener todos los videos de la galería
 */
function get_gallery_videos() {
    if (!file_exists(VIDEOS_FILE)) {
        return [];
    }
    $json = file_get_contents(VIDEOS_FILE);
    $videos = json_decode($json, true);
    return is_array($videos) ? $videos : [];
}

/**
 * Guardar la lista de videos
 */
function save_gallery_videos($videos) {
    $dir = dirname(VIDEOS_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $json = json_encode(array_values($videos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $bytes = file_put_contents(VIDEOS_FILE, $json, LOCK_EX);
    if ($bytes !== false) {
        @chmod(VIDEOS_FILE, 0666);
        clearstatcache(true, VIDEOS_FILE);
        return true;
    }
    return false;
}

/**
 * Extraer ID de un video de YouTube
 */
function get_youtube_id($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/i';
    if (preg_match($pattern, $url, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Obtener miniatura de video (YouTube o imagen personalizada)
 */
function get_video_thumbnail($url, $custom_thumb = '') {
    if (!empty($custom_thumb)) {
        return $custom_thumb;
    }
    $yt_id = get_youtube_id($url);
    if ($yt_id) {
        return 'https://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg';
    }
    return 'img/slider/2.jpg';
}
