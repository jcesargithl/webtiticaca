<?php
// admin/testimonios.php
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/reviews_helpers.php';
require_once __DIR__ . '/../includes/contact_helpers.php';

require_admin_login();

$reviews = get_all_reviews(false);
$unread_count = get_unread_messages_count();
$message = '';
$error_message = '';

if (isset($_GET['error']) && $_GET['error'] === 'save_failed') {
    $error_message = 'No se pudo guardar la reseña en el servidor (error de escritura en data/reviews.json). Verifica los permisos del directorio data/.';
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $message = "Reseña agregada con éxito.";
    } elseif ($_GET['msg'] === 'updated') {
        $message = "Reseña actualizada correctamente.";
    } elseif ($_GET['msg'] === 'deleted') {
        $message = "Reseña eliminada del sistema con éxito.";
    } elseif ($_GET['msg'] === 'status_updated') {
        $message = "Estado de visibilidad de la reseña actualizado.";
    }
}

// Estadísticas
$total_count = count($reviews);
$active_count = count(array_filter($reviews, function($r) { return !isset($r['active']) || $r['active'] === true; }));
$tripadvisor_count = 0;
$google_count = 0;
$linked_count = 0;

foreach ($reviews as $r) {
    $url = trim(!empty($r['review_url']) ? $r['review_url'] : ($r['google_url'] ?? ''));
    if ($url !== '') {
        $linked_count++;
        $p = get_review_platform_info($url);
        if ($p) {
            if ($p['platform'] === 'tripadvisor') $tripadvisor_count++;
            elseif ($p['platform'] === 'google') $google_count++;
        }
    }
}

$default_avatars = [
    'img/team/07.png' => 'Avatar 1 (Nidia)',
    'img/team/08.png' => 'Avatar 2 (Eberth)',
    'img/team/09.png' => 'Avatar 3 (Silvia)',
    'img/team/04.png' => 'Avatar 4 (Carlos)',
    'img/team/05.png' => 'Avatar 5 (Mariana)',
    'img/team/01.jpg' => 'Avatar 6 (Viajero)',
    'img/team/02.jpg' => 'Avatar 7 (Viajero)'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar Reseñas y Testimonios - Titicaca Experiences</title>
    <link rel="shortcut icon" href="../img/favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333;
        }
        .navbar-custom {
            background: #0f2454;
        }
        .brand-color {
            color: #2095AE;
        }
        .btn-brand {
            background-color: #2095AE;
            color: #fff;
            border: none;
        }
        .btn-brand:hover {
            background-color: #17788c;
            color: #fff;
        }
        .review-avatar-thumb {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e4e8;
        }
        .star-gold {
            color: #f5a623;
        }
        .avatar-choice-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
        }
        .avatar-choice-img:hover {
            transform: scale(1.1);
        }
        .avatar-choice-radio:checked + label .avatar-choice-img {
            border-color: #2095AE;
            box-shadow: 0 0 0 3px rgba(32, 149, 174, 0.3);
        }
        .google-pill {
            display: inline-flex;
            align-items: center;
            background: #e8f0fe;
            color: #1a73e8;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            border: 1px solid #d2e3fc;
            text-decoration: none;
        }
        .google-pill:hover {
            background: #d2e3fc;
            color: #174ea6;
        }
        .tripadvisor-pill {
            display: inline-flex;
            align-items: center;
            background: #e8f9f2;
            color: #007c50;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            border: 1px solid #c2f0dc;
            text-decoration: none;
        }
        .tripadvisor-pill:hover {
            background: #c2f0dc;
            color: #004f32;
        }
        .link-pill {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            color: #475569;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            text-decoration: none;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 18px;
        }
    </style>
</head>
<body>

    <!-- Barra de Navegación del Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-3 py-2 mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="../img/favicon.png" alt="" width="26" height="26" class="me-2">
                <span class="fw-bold">Admin Titicaca</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 text-white-50" href="index.php">
                            <i class="bi bi-newspaper me-1"></i> Blog
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 text-white-50" href="galeria.php">
                            <i class="bi bi-images me-1"></i> Fotos de Galería
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 text-white-50" href="videos.php">
                            <i class="bi bi-camera-video me-1"></i> Videos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 active bg-white text-dark fw-bold rounded" href="testimonios.php">
                            <i class="bi bi-chat-heart me-1 text-primary"></i> Reseñas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 text-white-50" href="mensajes.php">
                            <i class="bi bi-envelope me-1"></i> Mensajes
                            <?php if ($unread_count > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?= $unread_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                                <div class="d-flex align-items-center gap-2">
                    <a href="backup.php" class="btn btn-sm btn-success fw-semibold shadow-sm" title="Descargar copia de seguridad completa (.zip)">
                        <i class="bi bi-cloud-arrow-down-fill me-1"></i> Copia de Seguridad
                    </a>
                    <a href="../index.html" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-eye me-1"></i> Ver Sitio en Vivo
                    </a>
                    <a href="logout.php" class="btn btn-sm btn-light text-danger">
                        <i class="bi bi-power"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Encabezado y Métricas -->
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-7">
                <h3 class="fw-bold mb-1" style="color: #0f2454;">Administrador de Reseñas</h3>
                <p class="text-muted mb-0">Gestiona los testimonios con soporte para <strong>Google Maps</strong>, <strong>Tripadvisor</strong> y <strong>Web</strong>. En <a href="../resenas.php" target="_blank" class="fw-bold text-decoration-none">resenas.php</a> se muestra el catálogo completo, y en el carrusel de inicio se eligen 3 al azar.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="../resenas.php" target="_blank" class="btn btn-outline-primary px-3 py-2 fw-semibold shadow-sm me-2">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Ver Página Pública
                </a>
                <button type="button" class="btn btn-brand px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddReview">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Reseña
                </button>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center shadow-sm">
                    <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-chat-quote fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total de Reseñas</div>
                        <div class="fs-4 fw-bold text-dark"><?= $total_count ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center shadow-sm">
                    <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-check2-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Reseñas Activas</div>
                        <div class="fs-4 fw-bold text-dark"><?= $active_count ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center shadow-sm">
                    <div class="rounded-circle p-3 bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-link-45deg fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Reseñas con Enlace</div>
                        <div class="fs-5 fw-bold text-dark">
                            <?= $linked_count ?>
                            <span class="fs-6 fw-normal text-muted ms-1">(<?= $google_count ?> Google, <?= $tripadvisor_count ?> Tripadvisor)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Reseñas -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">Avatar</th>
                            <th style="min-width: 150px;">Viajero</th>
                            <th style="width: 110px;">Estrellas</th>
                            <th>Testimonio</th>
                            <th style="min-width: 150px;">Enlace Original</th>
                            <th style="width: 100px;">Estado</th>
                            <th class="text-end" style="min-width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reviews)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-square-dots fs-1 d-block mb-2 text-secondary"></i>
                                    No hay reseñas registradas aún. ¡Agrega la primera con el botón "+ Nueva Reseña"!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reviews as $r): 
                                $id = htmlspecialchars($r['id'] ?? '');
                                $name = htmlspecialchars($r['name'] ?? 'Viajero');
                                $role = htmlspecialchars($r['role'] ?? 'Viajero');
                                $text = htmlspecialchars($r['text'] ?? '');
                                $photo = htmlspecialchars(!empty($r['photo']) ? $r['photo'] : 'img/team/07.png');
                                $rating = isset($r['rating']) ? (int)$r['rating'] : 5;
                                $review_url = trim(!empty($r['review_url']) ? $r['review_url'] : ($r['google_url'] ?? ''));
                                $platform_info = get_review_platform_info($review_url);
                                $is_active = !isset($r['active']) || $r['active'] === true;
                            ?>
                                <tr>
                                    <td>
                                        <img src="../<?= $photo ?>" alt="<?= $name ?>" class="review-avatar-thumb">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $name ?></div>
                                        <div class="text-muted small"><?= $role ?></div>
                                    </td>
                                    <td>
                                        <div class="star-gold small">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $rating ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-secondary small" style="max-width: 320px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= $text ?>">
                                            <?= $text ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($review_url) && $platform_info): ?>
                                            <?php if ($platform_info['platform'] === 'tripadvisor'): ?>
                                                <a href="<?= htmlspecialchars($review_url) ?>" target="_blank" rel="noopener noreferrer" class="tripadvisor-pill" title="Probar enlace en Tripadvisor">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Tripadvisor
                                                </a>
                                            <?php elseif ($platform_info['platform'] === 'google'): ?>
                                                <a href="<?= htmlspecialchars($review_url) ?>" target="_blank" rel="noopener noreferrer" class="google-pill" title="Probar enlace en Google Maps">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Google Maps
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= htmlspecialchars($review_url) ?>" target="_blank" rel="noopener noreferrer" class="link-pill" title="Probar enlace">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Enlace Web
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border">Sin enlace</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="testimonios_action.php" class="d-inline">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <button type="submit" class="badge rounded-pill border-0 <?= $is_active ? 'bg-success' : 'bg-secondary' ?>" style="cursor: pointer;" title="Clic para cambiar visibilidad">
                                                <i class="bi bi-<?= $is_active ? 'check-circle' : 'eye-slash' ?> me-1"></i>
                                                <?= $is_active ? 'Activo' : 'Oculto' ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-primary me-1 btn-edit-review"
                                            data-id="<?= $id ?>"
                                            data-name="<?= $name ?>"
                                            data-role="<?= $role ?>"
                                            data-text="<?= $text ?>"
                                            data-rating="<?= $rating ?>"
                                            data-photo="<?= $photo ?>"
                                            data-review-url="<?= htmlspecialchars($review_url) ?>"
                                            data-active="<?= $is_active ? '1' : '0' ?>"
                                            title="Editar Reseña">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-danger btn-delete-review"
                                            data-id="<?= $id ?>"
                                            data-name="<?= $name ?>"
                                            title="Eliminar Reseña">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Reseña -->
    <div class="modal fade" id="modalAddReview" tabindex="-1" aria-labelledby="modalAddReviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="testimonios_action.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalAddReviewLabel" style="color: #0f2454;">
                            <i class="bi bi-plus-circle text-primary me-2"></i>Nueva Reseña de Viajero
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nombre del Viajero <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Ej: Nidia Perez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Rol / Subtítulo</label>
                                <input type="text" name="role" class="form-control" value="Viajero" placeholder="Ej: Viajero, Turista de Chile, etc.">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Calificación</label>
                                <select name="rating" class="form-select">
                                    <option value="5" selected>★★★★★ (5 Estrellas)</option>
                                    <option value="4">★★★★☆ (4 Estrellas)</option>
                                    <option value="3">★★★☆☆ (3 Estrellas)</option>
                                    <option value="2">★★☆☆☆ (2 Estrellas)</option>
                                    <option value="1">★☆☆☆☆ (1 Estrella)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                                    <span>URL de la Reseña (Google Maps o Tripadvisor) <span class="text-muted fw-normal">(Opcional)</span></span>
                                    <span id="add_detected_badge"></span>
                                </label>
                                <input type="url" name="review_url" id="add_review_url" class="form-control" placeholder="https://www.tripadvisor.com/... o https://www.google.com/maps/...">
                                <div class="form-text small">Pega el link de Google Maps o Tripadvisor. El sistema detectará automáticamente la plataforma y asignará su distintivo.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Comentario / Testimonio <span class="text-danger">*</span></label>
                                <textarea name="text" class="form-control" rows="3" placeholder="Escribe el testimonio o comentario que dejó el cliente..." required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small d-block">Avatar / Foto del Viajero</label>
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="text-muted small mb-2 fw-semibold">Elige un avatar predeterminado:</div>
                                    <div class="d-flex flex-wrap gap-3 mb-3">
                                        <?php $idx = 0; foreach ($default_avatars as $path => $lbl): $idx++; ?>
                                            <div>
                                                <input type="radio" name="selected_avatar" id="add_avatar_<?= $idx ?>" value="<?= $path ?>" class="d-none avatar-choice-radio" <?= $idx === 1 ? 'checked' : '' ?>>
                                                <label for="add_avatar_<?= $idx ?>" title="<?= $lbl ?>">
                                                    <img src="../<?= $path ?>" alt="<?= $lbl ?>" class="avatar-choice-img">
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="text-muted small mb-1 fw-semibold">O sube una foto personalizada desde tu equipo:</div>
                                    <input type="file" name="photo" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="active" value="1" id="addActive" checked>
                                    <label class="form-check-label small fw-semibold" for="addActive">
                                        Mostrar esta reseña en la web (Activo)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand px-4 fw-semibold">Guardar Reseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Reseña -->
    <div class="modal fade" id="modalEditReview" tabindex="-1" aria-labelledby="modalEditReviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="testimonios_action.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalEditReviewLabel" style="color: #0f2454;">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Editar Reseña
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nombre del Viajero <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Rol / Subtítulo</label>
                                <input type="text" name="role" id="edit_role" class="form-control" placeholder="Viajero">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Calificación</label>
                                <select name="rating" id="edit_rating" class="form-select">
                                    <option value="5">★★★★★ (5 Estrellas)</option>
                                    <option value="4">★★★★☆ (4 Estrellas)</option>
                                    <option value="3">★★★☆☆ (3 Estrellas)</option>
                                    <option value="2">★★☆☆☆ (2 Estrellas)</option>
                                    <option value="1">★☆☆☆☆ (1 Estrella)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                                    <span>URL de la Reseña (Google Maps o Tripadvisor) <span class="text-muted fw-normal">(Opcional)</span></span>
                                    <span id="edit_detected_badge"></span>
                                </label>
                                <input type="url" name="review_url" id="edit_review_url" class="form-control" placeholder="https://www.tripadvisor.com/... o https://www.google.com/maps/...">
                                <div class="form-text small">Pega el link de Google Maps o Tripadvisor. Si se deja vacío, la reseña no tendrá hipervínculo.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Comentario / Testimonio <span class="text-danger">*</span></label>
                                <textarea name="text" id="edit_text" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small d-block">Avatar Actual</label>
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="" id="edit_current_photo" class="review-avatar-thumb me-3" alt="Foto">
                                        <div>
                                            <div class="fw-semibold small">Foto asignada actualmente</div>
                                            <div class="text-muted small">Puedes seleccionar otro avatar o subir uno nuevo:</div>
                                        </div>
                                    </div>

                                    <div class="text-muted small mb-2 fw-semibold">Cambiar a un avatar predeterminado:</div>
                                    <div class="d-flex flex-wrap gap-3 mb-3">
                                        <?php $idx = 0; foreach ($default_avatars as $path => $lbl): $idx++; ?>
                                            <div>
                                                <input type="radio" name="selected_avatar" id="edit_avatar_<?= $idx ?>" value="<?= $path ?>" class="d-none avatar-choice-radio">
                                                <label for="edit_avatar_<?= $idx ?>" title="<?= $lbl ?>">
                                                    <img src="../<?= $path ?>" alt="<?= $lbl ?>" class="avatar-choice-img">
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="text-muted small mb-1 fw-semibold">O subir una nueva imagen:</div>
                                    <input type="file" name="photo" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="active" value="1" id="edit_active">
                                    <label class="form-check-label small fw-semibold" for="edit_active">
                                        Mostrar esta reseña en la web (Activo)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand px-4 fw-semibold">Actualizar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="modalDeleteReview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="testimonios_action.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Eliminar Reseña
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body small">
                        ¿Estás seguro de que deseas eliminar la reseña de <strong id="delete_name"></strong>? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-danger fw-semibold">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap y Controladores JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Detector visual dinámico de plataforma al escribir o pegar la URL
            function updatePlatformBadge(inputEl, badgeEl) {
                const val = (inputEl.value || '').trim().toLowerCase();
                if (!val) {
                    badgeEl.innerHTML = '';
                    return;
                }
                if (val.includes('tripadvisor')) {
                    badgeEl.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Tripadvisor detectado</span>';
                } else if (val.includes('google') || val.includes('goo.gl')) {
                    badgeEl.innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-check-circle me-1"></i>Google Maps detectado</span>';
                } else {
                    badgeEl.innerHTML = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-link-45deg me-1"></i>Enlace web</span>';
                }
            }

            const addUrlInput = document.getElementById('add_review_url');
            const addBadge = document.getElementById('add_detected_badge');
            if (addUrlInput && addBadge) {
                addUrlInput.addEventListener('input', () => updatePlatformBadge(addUrlInput, addBadge));
            }

            const editUrlInput = document.getElementById('edit_review_url');
            const editBadge = document.getElementById('edit_detected_badge');
            if (editUrlInput && editBadge) {
                editUrlInput.addEventListener('input', () => updatePlatformBadge(editUrlInput, editBadge));
            }

            // Manejar modal de edición
            const editModalEl = document.getElementById('modalEditReview');
            const editModal = new bootstrap.Modal(editModalEl);

            document.querySelectorAll('.btn-edit-review').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('edit_id').value = this.dataset.id;
                    document.getElementById('edit_name').value = this.dataset.name;
                    document.getElementById('edit_role').value = this.dataset.role;
                    document.getElementById('edit_text').value = this.dataset.text;
                    document.getElementById('edit_rating').value = this.dataset.rating;
                    
                    const reviewUrl = this.dataset.reviewUrl || '';
                    document.getElementById('edit_review_url').value = reviewUrl;
                    updatePlatformBadge(editUrlInput, editBadge);

                    document.getElementById('edit_active').checked = this.dataset.active === '1';

                    const currentPhoto = this.dataset.photo;
                    document.getElementById('edit_current_photo').src = '../' + currentPhoto;

                    // Desmarcar radios de avatar
                    document.querySelectorAll('#modalEditReview .avatar-choice-radio').forEach(r => {
                        r.checked = (r.value === currentPhoto);
                    });

                    editModal.show();
                });
            });

            // Manejar modal de eliminación
            const deleteModalEl = document.getElementById('modalDeleteReview');
            const deleteModal = new bootstrap.Modal(deleteModalEl);

            document.querySelectorAll('.btn-delete-review').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('delete_id').value = this.dataset.id;
                    document.getElementById('delete_name').textContent = this.dataset.name;
                    deleteModal.show();
                });
            });
        });
    </script>
</body>
</html>
