<?php
// admin/videos_action.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/gallery_helpers.php';

require_admin_login();

$action = $_POST['action'] ?? ($_GET['action'] ?? '');
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
    || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
    || isset($_POST['ajax']);

if (empty($action)) {
    $raw_input = file_get_contents('php://input');
    $json_data = json_decode($raw_input, true);
    if (is_array($json_data) && isset($json_data['action'])) {
        $action = $json_data['action'];
    }
}

$videos = get_gallery_videos();

switch ($action) {
    case 'reorder':
        $order_ids = [];
        if (isset($json_data['order']) && is_array($json_data['order'])) {
            $order_ids = $json_data['order'];
        } elseif (isset($_POST['order']) && is_array($_POST['order'])) {
            $order_ids = $_POST['order'];
        }

        if (!empty($order_ids)) {
            $indexed = [];
            foreach ($videos as $v) {
                $indexed[$v['id']] = $v;
            }

            $new_videos = [];
            foreach ($order_ids as $id) {
                if (isset($indexed[$id])) {
                    $new_videos[] = $indexed[$id];
                    unset($indexed[$id]);
                }
            }
            foreach ($indexed as $remaining) {
                $new_videos[] = $remaining;
            }

            save_gallery_videos($new_videos);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Orden de videos actualizado']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Sin IDs']);
        exit;

    case 'add':
        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');

        if ($url !== '') {
            $custom_thumb = '';
            if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['thumb']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed)) {
                    $dir = __DIR__ . '/../img/slider/galeria/uploads/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $thumb_name = 'vthumb_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['thumb']['tmp_name'], $dir . $thumb_name)) {
                        $custom_thumb = 'img/slider/galeria/uploads/' . $thumb_name;
                    }
                }
            }

            if ($custom_thumb === '') {
                $custom_thumb = get_video_thumbnail($url);
            }

            $new_vid = [
                'id' => 'vid_' . time() . '_' . rand(100, 999),
                'title' => $title !== '' ? $title : 'Video de Titicaca Experiences',
                'url' => $url,
                'thumb' => $custom_thumb
            ];

            $videos[] = $new_vid;
            save_gallery_videos($videos);
        }

        header('Location: videos.php?msg=added');
        exit;

    case 'edit':
        $id = trim($_POST['id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $url = trim($_POST['url'] ?? '');

        foreach ($videos as &$v) {
            if ($v['id'] === $id) {
                if ($title !== '') $v['title'] = $title;
                if ($url !== '') $v['url'] = $url;

                if (isset($_FILES['thumb']) && $_FILES['thumb']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['thumb']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($ext, $allowed)) {
                        $dir = __DIR__ . '/../img/slider/galeria/uploads/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $thumb_name = 'vthumb_' . time() . '_' . rand(100, 999) . '.' . $ext;
                        if (move_uploaded_file($_FILES['thumb']['tmp_name'], $dir . $thumb_name)) {
                            $v['thumb'] = 'img/slider/galeria/uploads/' . $thumb_name;
                        }
                    }
                } elseif (empty($v['thumb']) || strpos($v['thumb'], 'youtube.com') !== false) {
                    // Actualizar miniatura si cambió la URL de youtube
                    $v['thumb'] = get_video_thumbnail($url);
                }
                break;
            }
        }
        save_gallery_videos($videos);

        header('Location: videos.php?msg=updated');
        exit;

    case 'delete':
        $id = trim($_POST['id'] ?? ($_GET['id'] ?? ''));
        $new_videos = [];
        $deleted = false;
        foreach ($videos as $v) {
            if ($v['id'] === $id) {
                $deleted = true;
            } else {
                $new_videos[] = $v;
            }
        }
        if ($deleted) {
            save_gallery_videos($new_videos);
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $deleted]);
            exit;
        }

        header('Location: videos.php?msg=deleted');
        exit;

    default:
        header('Location: videos.php');
        exit;
}
