<?php
// admin/videos.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/gallery_helpers.php';

require_admin_login();

$videos = get_gallery_videos();
$message = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $message = "Video agregado a la galería con éxito.";
    } elseif ($_GET['msg'] === 'updated') {
        $message = "Video actualizado correctamente.";
    } elseif ($_GET['msg'] === 'deleted') {
        $message = "Video eliminado de la galería con éxito.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar Videos - Titicaca Experiences</title>
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
        .video-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e0e4e8;
            cursor: grab;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .video-card:active {
            cursor: grabbing;
        }
        .video-card.sortable-ghost {
            opacity: 0.35;
            border: 2px dashed #2095AE;
        }
        .video-thumb-wrap {
            position: relative;
            height: 180px;
            background: #000;
            overflow: hidden;
        }
        .video-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.88;
        }
        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            background: rgba(220, 53, 69, 0.9);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .pos-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(15, 36, 84, 0.85);
            color: #fff;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
        }
        .drag-handle {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 4px 6px;
            border-radius: 6px;
        }
        .toast-save {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1080;
            display: none;
        }
    </style>
</head>
<body>

    <!-- Barra de Navegación del Panel -->
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
                        <a class="nav-link px-3 py-1 active bg-white text-dark fw-bold rounded" href="videos.php">
                            <i class="bi bi-camera-video me-1 text-danger"></i> Videos
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="../galeria.php" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-eye me-1"></i> Ver Galería en Vivo
                    </a>
                    <a href="logout.php" class="btn btn-sm btn-light text-danger">
                        <i class="bi bi-power"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Encabezado de la página -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="color: #0f2454;">
                    <i class="bi bi-camera-video brand-color me-2"></i>Galería de Videos
                </h3>
                <p class="text-muted mb-0">
                    Total: <strong id="total-count"><?= count($videos) ?></strong> videos registrados. Pega enlaces de YouTube o Vimeo para publicarlos al instante.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-brand px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddVideo">
                    <i class="bi bi-plus-circle me-1"></i> + Agregar Video
                </button>
            </div>
        </div>

        <!-- Alerta Informativa -->
        <div class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-between mb-4 border-0 shadow-sm" style="background-color: #e8f4f8; color: #17788c;">
            <div>
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Arrastrar y Soltar:</strong> Puedes reordenar los videos arrastrando las tarjetas. El orden se guardará automáticamente.
            </div>
            <span class="badge bg-white text-dark border px-2 py-1"><i class="bi bi-arrows-move me-1"></i>Reordenar activo</span>
        </div>

        <!-- Cuadrícula de Videos -->
        <div class="row g-4" id="sortable-videos">
            <?php if (empty($videos)): ?>
                <div class="col-12 text-center py-5 bg-white rounded border">
                    <i class="bi bi-film text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Aún no hay videos registrados</h5>
                    <p class="text-muted small">Haz clic en "+ Agregar Video" para publicar tu primer video de YouTube o Vimeo.</p>
                </div>
            <?php else: ?>
                <?php foreach ($videos as $index => $v): 
                    $pos = $index + 1;
                    $title = $v['title'] ?? '';
                    $url = $v['url'] ?? '';
                    $thumb = !empty($v['thumb']) ? $v['thumb'] : get_video_thumbnail($url);
                    $full_thumb = (strpos($thumb, 'http') === 0) ? $thumb : '../' . $thumb;
                ?>
                    <div class="col-12 col-md-6 col-lg-4 video-item-col" data-id="<?= htmlspecialchars($v['id']) ?>">
                        <div class="video-card shadow-sm h-100 d-flex flex-column">
                            <div class="video-thumb-wrap">
                                <span class="pos-badge">#<?= $pos ?></span>
                                <span class="drag-handle" title="Arrastrar para mover"><i class="bi bi-grip-vertical"></i></span>
                                <img src="<?= htmlspecialchars($full_thumb) ?>" class="video-thumb" alt="<?= htmlspecialchars($title) ?>">
                                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="play-overlay text-decoration-none" title="Ver video">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-1 text-truncate" title="<?= htmlspecialchars($title) ?>" style="color: #0f2454;">
                                        <?= htmlspecialchars($title ?: 'Video sin título') ?>
                                    </h6>
                                    <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="text-muted small text-truncate d-block text-decoration-none">
                                        <i class="bi bi-link-45deg me-1"></i><?= htmlspecialchars($url) ?>
                                    </a>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <!-- Flechas mover -->
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary btn-move" data-dir="prev" title="Mover antes">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-move" data-dir="next" title="Mover después">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                    <!-- Editar y Borrar -->
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-edit-vid"
                                            data-id="<?= htmlspecialchars($v['id']) ?>"
                                            data-title="<?= htmlspecialchars($title) ?>"
                                            data-url="<?= htmlspecialchars($url) ?>"
                                            title="Editar video">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-delete-vid"
                                            data-id="<?= htmlspecialchars($v['id']) ?>"
                                            title="Eliminar video">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Agregar Video -->
    <div class="modal fade" id="modalAddVideo" tabindex="-1" aria-labelledby="modalAddVideoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="videos_action.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalAddVideoLabel">
                            <i class="bi bi-camera-video text-danger me-2"></i>Agregar Nuevo Video
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Título del Video</label>
                            <input type="text" name="title" class="form-control" placeholder="Ej: Tour Full Day Taquile y Uros" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Enlace de YouTube o Vimeo</label>
                            <input type="url" name="url" class="form-control" placeholder="https://youtu.be/... o https://vimeo.com/..." required>
                            <div class="form-text">La miniatura de YouTube se extraerá automáticamente si no subes una foto.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Foto de Portada Personalizada (Opcional)</label>
                            <input type="file" name="thumb" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Publicar Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Video -->
    <div class="modal fade" id="modalEditVideo" tabindex="-1" aria-labelledby="modalEditVideoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="videos_action.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit-vid-id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalEditVideoLabel">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Editar Video
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Título del Video</label>
                            <input type="text" name="title" id="edit-vid-title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Enlace de YouTube o Vimeo</label>
                            <input type="url" name="url" id="edit-vid-url" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Cambiar Portada Personalizada (Opcional)</label>
                            <input type="file" name="thumb" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand fw-semibold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Flotante -->
    <div id="saveToast" class="toast-save alert alert-success py-2 px-3 shadow-lg border-0 rounded-pill d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <span>¡Orden actualizado correctamente!</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const videoContainer = document.getElementById('sortable-videos');
        const saveToast = document.getElementById('saveToast');

        function showSaveToast(text = '¡Orden guardado!') {
            saveToast.querySelector('span').innerText = text;
            saveToast.style.display = 'flex';
            setTimeout(() => { saveToast.style.display = 'none'; }, 2500);
        }

        function updateBadges() {
            const items = videoContainer.querySelectorAll('.video-item-col');
            items.forEach((item, index) => {
                const badge = item.querySelector('.pos-badge');
                if (badge) badge.innerText = '#' + (index + 1);
            });
        }

        function saveOrderToServer() {
            const items = videoContainer.querySelectorAll('.video-item-col');
            const order = [];
            items.forEach(item => {
                const id = item.getAttribute('data-id');
                if (id) order.push(id);
            });

            fetch('videos_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action: 'reorder', order: order })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSaveToast('¡Orden guardado en la web!');
                }
            })
            .catch(err => console.error('Error al guardar:', err));
        }

        if (videoContainer) {
            new Sortable(videoContainer, {
                animation: 200,
                ghostClass: 'sortable-ghost',
                handle: '.video-card',
                onEnd: function () {
                    updateBadges();
                    saveOrderToServer();
                }
            });
        }

        videoContainer.addEventListener('click', function (e) {
            const btnMove = e.target.closest('.btn-move');
            if (btnMove) {
                const col = btnMove.closest('.video-item-col');
                const dir = btnMove.getAttribute('data-dir');
                if (dir === 'prev' && col.previousElementSibling) {
                    videoContainer.insertBefore(col, col.previousElementSibling);
                    updateBadges();
                    saveOrderToServer();
                } else if (dir === 'next' && col.nextElementSibling) {
                    videoContainer.insertBefore(col.nextElementSibling, col);
                    updateBadges();
                    saveOrderToServer();
                }
            }

            const btnEdit = e.target.closest('.btn-edit-vid');
            if (btnEdit) {
                document.getElementById('edit-vid-id').value = btnEdit.getAttribute('data-id');
                document.getElementById('edit-vid-title').value = btnEdit.getAttribute('data-title');
                document.getElementById('edit-vid-url').value = btnEdit.getAttribute('data-url');
                const editModal = new bootstrap.Modal(document.getElementById('modalEditVideo'));
                editModal.show();
            }

            const btnDelete = e.target.closest('.btn-delete-vid');
            if (btnDelete) {
                const col = btnDelete.closest('.video-item-col');
                const id = btnDelete.getAttribute('data-id');
                if (confirm('¿Estás seguro de que deseas eliminar este video?')) {
                    fetch('videos_action.php?action=delete&id=' + encodeURIComponent(id), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            col.remove();
                            updateBadges();
                            const totalCount = document.getElementById('total-count');
                            if (totalCount) totalCount.innerText = videoContainer.querySelectorAll('.video-item-col').length;
                            showSaveToast('Video eliminado');
                        }
                    })
                    .catch(err => console.error(err));
                }
            }
        });
    </script>
</body>
</html>
