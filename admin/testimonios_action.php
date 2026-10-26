<?php
// admin/testimonios_action.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/reviews_helpers.php';

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

$reviews = get_all_reviews(false);

switch ($action) {
    case 'add':
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? 'Viajero');
        $text = trim($_POST['text'] ?? '');
        $rating = isset($_POST['rating']) ? max(1, min(5, (int)$_POST['rating'])) : 5;
        $review_url = trim($_POST['review_url'] ?? ($_POST['google_url'] ?? ''));
        $active = isset($_POST['active']) ? (bool)$_POST['active'] : true;
        
        $selected_avatar = trim($_POST['selected_avatar'] ?? 'img/team/07.png');
        $final_photo = $selected_avatar;

        // Subida de imagen personalizada
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed)) {
                $dir = __DIR__ . '/../img/team/uploads/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $photo_name = 'avatar_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $photo_name)) {
                    $final_photo = 'img/team/uploads/' . $photo_name;
                }
            }
        }

        if ($name !== '' && $text !== '') {
            $new_rev = [
                'id' => 'rev_' . time() . '_' . rand(100, 999),
                'name' => $name,
                'role' => $role !== '' ? $role : 'Viajero',
                'text' => $text,
                'rating' => $rating,
                'photo' => $final_photo,
                'review_url' => $review_url,
                'google_url' => $review_url,
                'active' => $active,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Añadir al inicio para que aparezca primero en la tabla del admin
            array_unshift($reviews, $new_rev);
            save_reviews($reviews);
        }

        header('Location: testimonios.php?msg=added');
        exit;

    case 'edit':
        $id = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? 'Viajero');
        $text = trim($_POST['text'] ?? '');
        $rating = isset($_POST['rating']) ? max(1, min(5, (int)$_POST['rating'])) : 5;
        $review_url = trim($_POST['review_url'] ?? ($_POST['google_url'] ?? ''));
        $active = isset($_POST['active']) && $_POST['active'] == '1';

        $found = false;
        foreach ($reviews as &$r) {
            if ($r['id'] === $id) {
                $found = true;
                $final_photo = $r['photo'] ?? 'img/team/07.png';

                // Si seleccionó otro avatar predeterminado
                if (!empty($_POST['selected_avatar'])) {
                    $final_photo = trim($_POST['selected_avatar']);
                }

                // Si subió un archivo nuevo
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($ext, $allowed)) {
                        $dir = __DIR__ . '/../img/team/uploads/';
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }
                        $photo_name = 'avatar_' . time() . '_' . rand(100, 999) . '.' . $ext;
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $photo_name)) {
                            $final_photo = 'img/team/uploads/' . $photo_name;
                        }
                    }
                }

                $r['name'] = $name !== '' ? $name : $r['name'];
                $r['role'] = $role !== '' ? $role : 'Viajero';
                $r['text'] = $text !== '' ? $text : $r['text'];
                $r['rating'] = $rating;
                $r['photo'] = $final_photo;
                $r['review_url'] = $review_url;
                $r['google_url'] = $review_url;
                $r['active'] = $active;
                $r['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($r);

        if ($found) {
            save_reviews($reviews);
        }

        header('Location: testimonios.php?msg=updated');
        exit;

    case 'delete':
        $id = trim($_POST['id'] ?? ($_GET['id'] ?? ''));
        if ($id !== '') {
            $filtered = [];
            foreach ($reviews as $r) {
                if ($r['id'] !== $id) {
                    $filtered[] = $r;
                }
            }
            save_reviews($filtered);
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('Location: testimonios.php?msg=deleted');
        exit;

    case 'toggle_status':
        $id = trim($_POST['id'] ?? ($_GET['id'] ?? ''));
        $new_status = null;
        if ($id !== '') {
            foreach ($reviews as &$r) {
                if ($r['id'] === $id) {
                    $r['active'] = empty($r['active']) ? true : false;
                    $new_status = $r['active'];
                    break;
                }
            }
            unset($r);
            save_reviews($reviews);
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'active' => $new_status]);
            exit;
        }

        header('Location: testimonios.php?msg=status_updated');
        exit;

    default:
        header('Location: testimonios.php');
        exit;
}
