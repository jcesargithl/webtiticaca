<?php
// admin/galeria_action.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/gallery_helpers.php';

require_admin_login();

// Determinar acción
$action = $_POST['action'] ?? ($_GET['action'] ?? '');
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
    || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
    || isset($_POST['ajax']);

// Si viene JSON crudo en el body
if (empty($action)) {
    $raw_input = file_get_contents('php://input');
    $json_data = json_decode($raw_input, true);
    if (is_array($json_data) && isset($json_data['action'])) {
        $action = $json_data['action'];
    }
}

$images = get_gallery_images();

switch ($action) {
    case 'reorder':
        // Reordenar imágenes según el array de IDs recibido
        $order_ids = [];
        if (isset($json_data['order']) && is_array($json_data['order'])) {
            $order_ids = $json_data['order'];
        } elseif (isset($_POST['order']) && is_array($_POST['order'])) {
            $order_ids = $_POST['order'];
        }

        if (!empty($order_ids)) {
            // Mapear imágenes por ID
            $indexed = [];
            foreach ($images as $img) {
                $indexed[$img['id']] = $img;
            }

            $new_images = [];
            foreach ($order_ids as $id) {
                if (isset($indexed[$id])) {
                    $new_images[] = $indexed[$id];
                    unset($indexed[$id]);
                }
            }
            // Agregar cualquier imagen que haya quedado pendiente
            foreach ($indexed as $remaining) {
                $new_images[] = $remaining;
            }

            save_gallery_images($new_images);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Orden actualizado con éxito']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'No se recibieron IDs para reordenar']);
        exit;

    case 'upload':
        // Subida de una o múltiples fotos
        $uploaded_count = 0;
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $upload_dir = __DIR__ . '/../img/slider/galeria/uploads/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
            $file_count = count($_FILES['photos']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                    $orig_name = $_FILES['photos']['name'][$i];
                    $tmp_name = $_FILES['photos']['tmp_name'][$i];
                    $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed_exts)) {
                        $new_name = 'gal_' . time() . '_' . rand(100, 999) . '_' . ($i + 1) . '.' . $ext;
                        $dest = $upload_dir . $new_name;

                        if (move_uploaded_file($tmp_name, $dest)) {
                            // Crear registro de imagen
                            $title_clean = pathinfo($orig_name, PATHINFO_FILENAME);
                            $title_clean = str_replace(['_', '-'], ' ', $title_clean);
                            $title_clean = ucfirst(trim($title_clean));

                            $new_item = [
                                'id' => 'img_' . time() . '_' . rand(1000, 9999),
                                'src' => 'img/slider/galeria/uploads/' . $new_name,
                                'title' => $title_clean,
                                'alt' => $title_clean . ' - Titicaca Experiences'
                            ];

                            // Opcional: poner al inicio o al final. Agregamos al final (o inicio si se desea)
                            $images[] = $new_item;
                            $uploaded_count++;
                        }
                    }
                }
            }
        } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $orig_name = $_FILES['photo']['name'];
            $tmp_name = $_FILES['photo']['tmp_name'];
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed_exts)) {
                $new_name = 'gal_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $dest = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $dest)) {
                    $custom_title = trim($_POST['title'] ?? '');
                    if ($custom_title === '') {
                        $custom_title = ucfirst(str_replace(['_', '-'], ' ', pathinfo($orig_name, PATHINFO_FILENAME)));
                    }

                    $new_item = [
                        'id' => 'img_' . time() . '_' . rand(1000, 9999),
                        'src' => 'img/slider/galeria/uploads/' . $new_name,
                        'title' => $custom_title,
                        'alt' => $custom_title . ' - Titicaca Experiences'
                    ];

                    $images[] = $new_item;
                    $uploaded_count++;
                }
            }
        }

        if ($uploaded_count > 0) {
            save_gallery_images($images);
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'count' => $uploaded_count]);
            exit;
        }

        header('Location: galeria.php?msg=uploaded&count=' . $uploaded_count);
        exit;

    case 'edit':
        $id = trim($_POST['id'] ?? ($json_data['id'] ?? ''));
        $title = trim($_POST['title'] ?? ($json_data['title'] ?? ''));
        $alt = trim($_POST['alt'] ?? ($json_data['alt'] ?? ''));

        $found = false;
        foreach ($images as &$img) {
            if ($img['id'] === $id) {
                $img['title'] = $title;
                $img['alt'] = $alt !== '' ? $alt : ($title !== '' ? $title . ' - Titicaca Experiences' : 'Titicaca Experiences');
                $found = true;
                break;
            }
        }

        if ($found) {
            save_gallery_images($images);
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $found]);
            exit;
        }

        header('Location: galeria.php?msg=updated');
        exit;

    case 'delete':
        $id = trim($_POST['id'] ?? ($_GET['id'] ?? ($json_data['id'] ?? '')));

        $new_images = [];
        $deleted = false;
        foreach ($images as $img) {
            if ($img['id'] === $id) {
                $deleted = true;
                // Si la imagen es un upload reciente en uploads/, podríamos borrar el archivo
                // Pero mantenemos integridad evitando borrar fotos base g1..g20
                if (strpos($img['src'], 'uploads/') !== false) {
                    $file_to_del = __DIR__ . '/../' . $img['src'];
                    if (file_exists($file_to_del)) {
                        @unlink($file_to_del);
                    }
                }
            } else {
                $new_images[] = $img;
            }
        }

        if ($deleted) {
            save_gallery_images($new_images);
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $deleted]);
            exit;
        }

        header('Location: galeria.php?msg=deleted');
        exit;

    default:
        header('Location: galeria.php');
        exit;
}
