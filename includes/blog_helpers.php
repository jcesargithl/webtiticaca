<?php
// includes/blog_helpers.php

define('POSTS_FILE', __DIR__ . '/../data/posts.json');

function get_all_posts($sort_desc = true) {
    if (!file_exists(POSTS_FILE)) {
        return [];
    }
    $json = file_get_contents(POSTS_FILE);
    $posts = json_decode($json, true);
    if (!is_array($posts)) {
        return [];
    }
    if ($sort_desc) {
        usort($posts, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
    }
    return $posts;
}

function save_posts($posts) {
    $dir = dirname(POSTS_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $json = json_encode(array_values($posts), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $bytes = file_put_contents(POSTS_FILE, $json, LOCK_EX);
    if ($bytes !== false) {
        @chmod(POSTS_FILE, 0666);
        clearstatcache(true, POSTS_FILE);
        return true;
    }
    return false;
}

function get_post_by_slug($slug) {
    $posts = get_all_posts(false);
    foreach ($posts as $post) {
        if ($post['slug'] === $slug || (string)$post['id'] === (string)$slug) {
            return $post;
        }
    }
    return null;
}

function get_recent_posts($limit = 3, $exclude_slug = '') {
    $posts = get_all_posts(true);
    $recent = [];
    foreach ($posts as $post) {
        if ($exclude_slug && $post['slug'] === $exclude_slug) {
            continue;
        }
        $recent[] = $post;
        if (count($recent) >= $limit) {
            break;
        }
    }
    return $recent;
}

function get_all_categories() {
    $posts = get_all_posts(false);
    $categories = [];
    foreach ($posts as $post) {
        $cat = trim($post['category'] ?? '');
        if ($cat !== '') {
            $categories[$cat] = ($categories[$cat] ?? 0) + 1;
        }
    }
    ksort($categories);
    return $categories;
}

function get_all_tags() {
    $posts = get_all_posts(false);
    $tags = [];
    foreach ($posts as $post) {
        if (!empty($post['tags']) && is_array($post['tags'])) {
            foreach ($post['tags'] as $tag) {
                $t = trim($tag);
                if ($t !== '' && !in_array($t, $tags)) {
                    $tags[] = $t;
                }
            }
        }
    }
    sort($tags);
    return $tags;
}

function search_posts($query) {
    $posts = get_all_posts(true);
    $query = mb_strtolower(trim($query), 'UTF-8');
    if ($query === '') {
        return $posts;
    }
    $results = [];
    foreach ($posts as $post) {
        $title = mb_strtolower($post['title'] ?? '', 'UTF-8');
        $content = mb_strtolower(strip_tags($post['content'] ?? ''), 'UTF-8');
        $cat = mb_strtolower($post['category'] ?? '', 'UTF-8');
        $tags_str = mb_strtolower(implode(' ', $post['tags'] ?? []), 'UTF-8');
        
        if (strpos($title, $query) !== false ||
            strpos($content, $query) !== false ||
            strpos($cat, $query) !== false ||
            strpos($tags_str, $query) !== false) {
            $results[] = $post;
        }
    }
    return $results;
}

function filter_posts_by_category($cat) {
    $posts = get_all_posts(true);
    $results = [];
    foreach ($posts as $post) {
        if (strcasecmp($post['category'] ?? '', $cat) === 0) {
            $results[] = $post;
        }
    }
    return $results;
}

function filter_posts_by_tag($tag) {
    $posts = get_all_posts(true);
    $results = [];
    foreach ($posts as $post) {
        if (!empty($post['tags']) && is_array($post['tags'])) {
            foreach ($post['tags'] as $t) {
                if (strcasecmp($t, $tag) === 0) {
                    $results[] = $post;
                    break;
                }
            }
        }
    }
    return $results;
}

function slugify($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'post-' . time() : $text;
}

function get_spanish_date_parts($date_str) {
    $timestamp = strtotime($date_str);
    if (!$timestamp) {
        $timestamp = time();
    }
    $day = date('d', $timestamp);
    $month_num = (int)date('m', $timestamp);
    $year = date('Y', $timestamp);
    
    $months_short = [
        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
        9 => 'Set', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
    ];
    $months_full = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Setiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    return [
        'day' => $day,
        'month_short' => $months_short[$month_num] ?? 'Mes',
        'month_full' => $months_full[$month_num] ?? 'Mes',
        'year' => $year,
        'full' => ((int)$day) . ' ' . ($months_full[$month_num] ?? '') . ' ' . $year
    ];
}
