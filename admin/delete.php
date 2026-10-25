<?php
// admin/delete.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/blog_helpers.php';

require_admin_login();

$id = isset($_GET['id']) ? trim($_GET['id']) : '';

if ($id !== '') {
    $posts = get_all_posts(false);
    $new_posts = [];
    foreach ($posts as $p) {
        if ((string)$p['id'] !== $id && $p['slug'] !== $id) {
            $new_posts[] = $p;
        }
    }
    save_posts($new_posts);
}

header('Location: index.php?msg=deleted');
exit;
