<?php
// admin/galeria.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/gallery_helpers.php';
require_once __DIR__ . '/../includes/contact_helpers.php';

require_admin_login();

$images = get_gallery_images();
$unread_count = get_unread_messages_count();
$message = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'uploaded') {
        $count = (int)($_GET['count'] ?? 1);
        $message = "Se " . ($count > 1 ? "han subido {$count} imágenes" : "ha subido 1 imagen") . " con éxito.";
    } elseif ($_GET['msg'] === 'updated') {
        $message = "Detalles de la imagen actualizados correctamente.";
    } elseif ($_GET['msg'] === 'deleted') {
        $message = "Imagen eliminada de la galería con éxito.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar Galería de Fotos - Titicaca Experiences</title>
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
        .gallery-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e0e4e8;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            cursor: grab;
        }
        .gallery-card:active {
            cursor: grabbing;
        }
        .gallery-card.sortable-ghost {
            opacity: 0.35;
            transform: scale(0.96);
            border: 2px dashed #2095AE;
        }
        .gallery-card.sortable-chosen {
            box-shadow: 0 10px 25px rgba(32, 149, 174, 0.35);
        }
        .gallery-thumb-wrap {
            position: relative;
            height: 160px;
            background: #eee;
            overflow: hidden;
        }
        .gallery-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
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
            backdrop-filter: blur(4px);
        }
        .drag-handle {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 4px 6px;
            border-radius: 6px;
            cursor: grab;
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
                        <a class="nav-link px-3 py-1 active bg-white text-dark fw-bold rounded" href="galeria.php">
                            <i class="bi bi-images me-1 text-primary"></i> Fotos de Galería
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 text-white-50" href="videos.php">
                            <i class="bi bi-camera-video me-1"></i> Videos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 text-white-50" href="testimonios.php">
                            <i class="bi bi-chat-heart me-1"></i> Reseñas
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
                    <i class="bi bi-images brand-color me-2"></i>Galería de Fotos
                </h3>
                <p class="text-muted mb-0">
                    Total: <strong id="total-count"><?= count($images) ?></strong> fotos registradas. Arrastra las fotos para cambiar su orden en la web.
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-brand px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalUpload">
                    <i class="bi bi-cloud-arrow-up me-1"></i> + Subir Fotos
                </button>
            </div>
        </div>

        <!-- Alerta Informativa sobre Reordenar -->
        <div class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-between mb-4 border-0 shadow-sm" style="background-color: #e8f4f8; color: #17788c;">
            <div>
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Arrastrar y Soltar:</strong> Mueve cualquier foto a una nueva posición. El nuevo orden se guarda <strong>automáticamente</strong> en la web.
            </div>
            <span class="badge bg-white text-dark border px-2 py-1"><i class="bi bi-arrows-move me-1"></i>Drag & Drop activo</span>
        </div>

        <!-- Cuadrícula de Fotos (Sortable Grid) -->
        <div class="row g-3" id="sortable-gallery">
            <?php if (empty($images)): ?>
                <div class="col-12 text-center py-5 bg-white rounded border">
                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Aún no hay fotos en la galería</h5>
                    <p class="text-muted small">Haz clic en "+ Subir Fotos" para agregar tus primeras imágenes.</p>
                </div>
            <?php else: ?>
                <?php foreach ($images as $index => $img): 
                    $pos = $index + 1;
                    $title = $img['title'] ?? '';
                    $alt = $img['alt'] ?? '';
                ?>
                    <div class="col-6 col-md-4 col-lg-3 gallery-item-col" data-id="<?= htmlspecialchars($img['id']) ?>">
                        <div class="gallery-card shadow-sm h-100 d-flex flex-column">
                            <div class="gallery-thumb-wrap">
                                <span class="pos-badge">#<?= $pos ?></span>
                                <span class="drag-handle" title="Arrastrar para mover"><i class="bi bi-grip-vertical"></i></span>
                                <img src="../<?= htmlspecialchars($img['src']) ?>" class="gallery-thumb" alt="<?= htmlspecialchars($alt) ?>" loading="lazy">
                            </div>
                            <div class="p-2 d-flex flex-column flex-grow-1 justify-content-between">
                                <div class="mb-2">
                                    <div class="fw-semibold text-truncate small" title="<?= htmlspecialchars($title) ?>">
                                        <?= htmlspecialchars($title ?: 'Sin título') ?>
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 11px;">
                                        <?= htmlspecialchars($img['src']) ?>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <!-- Botones mover paso a paso -->
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary btn-move" data-dir="prev" title="Mover a la izquierda / antes">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-move" data-dir="next" title="Mover a la derecha / después">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                    <!-- Editar y Borrar -->
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary btn-edit" 
                                            data-id="<?= htmlspecialchars($img['id']) ?>"
                                            data-title="<?= htmlspecialchars($title) ?>"
                                            data-alt="<?= htmlspecialchars($alt) ?>"
                                            data-src="../<?= htmlspecialchars($img['src']) ?>"
                                            title="Editar título y descripción">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-delete" 
                                            data-id="<?= htmlspecialchars($img['id']) ?>"
                                            title="Quitar foto de la galería">
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

    <!-- Modal para Subir Fotos -->
    <div class="modal fade" id="modalUpload" tabindex="-1" aria-labelledby="modalUploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="galeria_action.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalUploadLabel">
                            <i class="bi bi-cloud-arrow-up text-primary me-2"></i>Subir Nuevas Fotos
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Selecciona una o varias imágenes</label>
                            <input type="file" name="photos[]" id="filePhotos" class="form-control" multiple accept="image/jpeg,image/png,image/webp" required>
                            <div class="form-text">Formatos permitidos: JPG, PNG, WebP. Puedes seleccionar varios archivos a la vez.</div>
                        </div>
                        <div id="upload-preview" class="row g-2 mt-2" style="max-height: 180px; overflow-y: auto;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand fw-semibold">
                            <i class="bi bi-upload me-1"></i> Subir a la Galería
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Foto -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="galeria_action.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalEditLabel">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Editar Datos de la Foto
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <img id="edit-preview" src="" alt="" class="rounded border" style="max-height: 150px; max-width: 100%; object-fit: contain;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Título / Pie de Foto (Aparece en el Zoom)</label>
                            <input type="text" name="title" id="edit-title" class="form-control" placeholder="Ej: Atardecer en el Lago Titicaca">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Texto Alternativo (Alt / SEO de Google)</label>
                            <input type="text" name="alt" id="edit-alt" class="form-control" placeholder="Ej: Vista del Lago Titicaca en Puno">
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

    <!-- Toast Flotante de Confirmación de Guardado -->
    <div id="saveToast" class="toast-save alert alert-success py-2 px-3 shadow-lg border-0 rounded-pill d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <span>¡Orden actualizado correctamente!</span>
    </div>

    <!-- Librerías JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const galleryContainer = document.getElementById('sortable-gallery');
        const saveToast = document.getElementById('saveToast');

        function showSaveToast(text = '¡Orden guardado con éxito!') {
            saveToast.querySelector('span').innerText = text;
            saveToast.style.display = 'flex';
            setTimeout(() => {
                saveToast.style.display = 'none';
            }, 2500);
        }

        function updateBadges() {
            const items = galleryContainer.querySelectorAll('.gallery-item-col');
            items.forEach((item, index) => {
                const badge = item.querySelector('.pos-badge');
                if (badge) badge.innerText = '#' + (index + 1);
            });
        }

        function saveOrderToServer() {
            const items = galleryContainer.querySelectorAll('.gallery-item-col');
            const order = [];
            items.forEach(item => {
                const id = item.getAttribute('data-id');
                if (id) order.push(id);
            });

            fetch('galeria_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: 'reorder',
                    order: order
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSaveToast('¡Orden guardado en la web!');
                }
            })
            .catch(err => console.error('Error al guardar orden:', err));
        }

        // Inicializar Drag & Drop con SortableJS
        if (galleryContainer) {
            new Sortable(galleryContainer, {
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                handle: '.gallery-card',
                onEnd: function () {
                    updateBadges();
                    saveOrderToServer();
                }
            });
        }

        // Botones paso a paso (mover anterior / siguiente)
        galleryContainer.addEventListener('click', function (e) {
            const btnMove = e.target.closest('.btn-move');
            if (btnMove) {
                const col = btnMove.closest('.gallery-item-col');
                const dir = btnMove.getAttribute('data-dir');
                if (dir === 'prev' && col.previousElementSibling) {
                    galleryContainer.insertBefore(col, col.previousElementSibling);
                    updateBadges();
                    saveOrderToServer();
                } else if (dir === 'next' && col.nextElementSibling) {
                    galleryContainer.insertBefore(col.nextElementSibling, col);
                    updateBadges();
                    saveOrderToServer();
                }
            }

            // Botón Editar
            const btnEdit = e.target.closest('.btn-edit');
            if (btnEdit) {
                const id = btnEdit.getAttribute('data-id');
                const title = btnEdit.getAttribute('data-title');
                const alt = btnEdit.getAttribute('data-alt');
                const src = btnEdit.getAttribute('data-src');

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-title').value = title;
                document.getElementById('edit-alt').value = alt;
                document.getElementById('edit-preview').src = src;

                const editModal = new bootstrap.Modal(document.getElementById('modalEdit'));
                editModal.show();
            }

            // Botón Eliminar
            const btnDelete = e.target.closest('.btn-delete');
            if (btnDelete) {
                const col = btnDelete.closest('.gallery-item-col');
                const id = btnDelete.getAttribute('data-id');
                if (confirm('¿Estás seguro de que deseas quitar esta foto de la galería?')) {
                    fetch('galeria_action.php?action=delete&id=' + encodeURIComponent(id), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            col.remove();
                            updateBadges();
                            const totalCount = document.getElementById('total-count');
                            if (totalCount) totalCount.innerText = galleryContainer.querySelectorAll('.gallery-item-col').length;
                            showSaveToast('Foto eliminada');
                        }
                    })
                    .catch(err => console.error('Error al eliminar:', err));
                }
            }
        });

        // Vista previa de archivos seleccionados para subir
        const filePhotos = document.getElementById('filePhotos');
        const uploadPreview = document.getElementById('upload-preview');
        if (filePhotos) {
            filePhotos.addEventListener('change', function () {
                uploadPreview.innerHTML = '';
                if (this.files) {
                    Array.from(this.files).forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const div = document.createElement('div');
                                div.className = 'col-3';
                                div.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded border" style="height: 60px; width: 100%; object-fit: cover;">`;
                                uploadPreview.appendChild(div);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
