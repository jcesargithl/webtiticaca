<?php
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
// admin/mensajes.php - Panel de Mensajes de Contacto y Solicitudes de Reserva
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/contact_helpers.php';

require_admin_login();

// 1. Exportar a CSV para Excel
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    $messages = get_all_messages();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=contactos_y_reservas_' . date('Ymd_His') . '.csv');
    $out = fopen('php://output', 'w');
    // BOM UTF-8 para compatibilidad nativa con Excel
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($out, ['ID', 'Fecha y Hora', 'Tipo', 'Estado', 'Nombre', 'Correo', 'Teléfono', 'Asunto / Tour', 'Fecha Solicitada', 'Personas', 'Mensaje', 'IP Origen', 'Email Despachado']);
    foreach ($messages as $m) {
        fputcsv($out, [
            $m['id'] ?? '',
            $m['created_at'] ?? '',
            ucfirst($m['type'] ?? 'contacto'),
            ucfirst($m['status'] ?? 'nuevo'),
            $m['name'] ?? '',
            $m['email'] ?? '',
            $m['phone'] ?? '',
            $m['subject'] ?? ($m['tour'] ?? ''),
            $m['fecha'] ?? '',
            $m['people'] ?? '',
            $m['message'] ?? '',
            $m['ip'] ?? '',
            !empty($m['email_sent']) ? 'Sí' : 'No'
        ]);
    }
    fclose($out);
    exit;
}

// 2. Cambiar estado de leído / nuevo
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
    $id = trim($_GET['id'] ?? '');
    $current = trim($_GET['current'] ?? 'nuevo');
    $new_status = ($current === 'nuevo') ? 'leido' : 'nuevo';
    if ($id !== '') {
        update_message_status($id, $new_status);
    }
    $filter_param = isset($_GET['filter']) ? '?filter=' . urlencode($_GET['filter']) : '';
    header('Location: mensajes.php' . $filter_param);
    exit;
}

// 3. Eliminar mensaje
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = trim($_GET['id'] ?? '');
    if ($id !== '') {
        delete_message($id);
    }
    $filter_param = isset($_GET['filter']) ? '&filter=' . urlencode($_GET['filter']) : '';
    header('Location: mensajes.php?msg=deleted' . $filter_param);
    exit;
}

$all_messages = get_all_messages();
$unread_count = get_unread_contacts_count();
$unread_reservations = get_unread_reservations_count();

// Estadísticas
$total_count    = count($all_messages);
$contacto_count = 0;
$reserva_count  = 0;
foreach ($all_messages as $m) {
    if (($m['type'] ?? 'contacto') === 'reserva') {
        $reserva_count++;
    } else {
        $contacto_count++;
    }
}

// Filtrar según parámetro
$active_filter = $_GET['filter'] ?? 'todos';
$filtered_messages = [];
foreach ($all_messages as $m) {
    if ($active_filter === 'nuevos') {
        if (($m['status'] ?? 'nuevo') === 'nuevo') {
            $filtered_messages[] = $m;
        }
    } elseif ($active_filter === 'contacto') {
        if (($m['type'] ?? 'contacto') === 'contacto') {
            $filtered_messages[] = $m;
        }
    } elseif ($active_filter === 'reserva') {
        if (($m['type'] ?? 'contacto') === 'reserva') {
            $filtered_messages[] = $m;
        }
    } else {
        $filtered_messages[] = $m;
    }
}

$message = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Mensaje eliminado del registro.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mensajes y Reservas - Admin Titicaca</title>
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
        .stat-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .message-row-nuevo {
            background-color: #f0f9ff !important;
            font-weight: 500;
        }
        .badge-contacto {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }
        .badge-reserva {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: 600;
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
                        <a class="nav-link px-3 py-1 active bg-white text-dark fw-bold rounded" href="mensajes.php">
                            <i class="bi bi-chat-dots me-1 text-primary"></i> Contactos
                            <?php if ($unread_count > 0): ?>
                                <span class="badge bg-secondary rounded-pill ms-1"><?= $unread_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 text-white-50" href="reservas.php">
                            <i class="bi bi-calendar-check me-1"></i> Reservas
                            <?php if ($unread_reservations > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?= $unread_reservations ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                                <div class="d-flex align-items-center gap-2">
                    <a href="backup.php" class="btn btn-sm btn-success fw-semibold shadow-sm" title="Descargar copia de seguridad completa (.zip)">
                        <i class="bi bi-cloud-arrow-down-fill me-1"></i> Copia de Seguridad
                    </a>
                    <a href="../contacto.html" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-eye me-1"></i> Ver Formulario en Vivo
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

        <!-- Encabezado con Título y Exportar -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1" style="color: #0f2454;">Bandeja de Mensajes y Reservas</h3>
                <p class="text-muted small mb-0">Respaldo seguro de todas las consultas recibidas desde la página web</p>
            </div>
            <div>
                <a href="mensajes.php?action=export_csv" class="btn btn-success fw-semibold">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar a Excel (CSV)
                </a>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="mensajes.php?filter=todos" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'todos' ? 'border border-primary' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Total Mensajes</span>
                                <h3 class="fw-bold mb-0 text-dark"><?= $total_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-light text-primary fs-4">
                                <i class="bi bi-inbox"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="mensajes.php?filter=nuevos" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'nuevos' ? 'border border-danger' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Sin Leer</span>
                                <h3 class="fw-bold mb-0 text-danger"><?= $unread_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-danger-subtle text-danger fs-4">
                                <i class="bi bi-envelope-exclamation"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="mensajes.php?filter=contacto" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'contacto' ? 'border border-info' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Contactos</span>
                                <h3 class="fw-bold mb-0 text-info-emphasis"><?= $contacto_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-info-subtle text-info fs-4">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="mensajes.php?filter=reserva" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'reserva' ? 'border border-warning' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Reservas</span>
                                <h3 class="fw-bold mb-0 text-warning-emphasis"><?= $reserva_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-warning-subtle text-warning fs-4">
                                <i class="bi bi-calendar2-check"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Filtros y Buscador Rápido -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-7">
                        <ul class="nav nav-pills gap-1">
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'todos' ? 'active' : '' ?>" href="mensajes.php?filter=todos">Todos (<?= $total_count ?>)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'nuevos' ? 'active bg-danger' : '' ?>" href="mensajes.php?filter=nuevos">
                                    Sin leer (<?= $unread_count ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'contacto' ? 'active bg-info text-dark' : '' ?>" href="mensajes.php?filter=contacto">Contactos (<?= $contacto_count ?>)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'reserva' ? 'active bg-warning text-dark' : '' ?>" href="mensajes.php?filter=reserva">Reservas (<?= $reserva_count ?>)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, correo, teléfono o asunto...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Mensajes -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="messagesTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">Fecha</th>
                            <th style="width: 100px;">Tipo</th>
                            <th style="width: 220px;">Cliente</th>
                            <th>Asunto / Consulta</th>
                            <th style="width: 140px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_messages)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    No hay mensajes registrados en esta sección.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_messages as $idx => $m): 
                                $is_nuevo = ($m['status'] ?? 'nuevo') === 'nuevo';
                                $clean_phone = preg_replace('/[^0-9]/', '', $m['phone'] ?? '');
                                $modal_id = 'modalMsg' . $idx;
                            ?>
                                <tr class="<?= $is_nuevo ? 'message-row-nuevo' : '' ?> search-item">
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($m['created_at'] ?? 'now'))) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars(date('H:i', strtotime($m['created_at'] ?? 'now'))) ?> hrs
                                        </div>
                                        <?php if ($is_nuevo): ?>
                                            <span class="badge bg-danger rounded-pill mt-1" style="font-size: 0.65rem;">NUEVO</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill mt-1" style="font-size: 0.65rem;">LEÍDO</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (($m['type'] ?? 'contacto') === 'reserva'): ?>
                                            <span class="badge badge-reserva px-2 py-1 rounded">
                                                <i class="bi bi-calendar-check me-1"></i> Reserva
                                            </span>
                                            <?php if (!empty($m['fecha'])): ?>
                                                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock me-1"></i><?= htmlspecialchars($m['fecha']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($m['people'])): ?>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="bi bi-people me-1"></i><?= htmlspecialchars($m['people']) ?> pers.
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-contacto px-2 py-1 rounded">
                                                <i class="bi bi-chat-text me-1"></i> Contacto
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($m['name'] ?? 'Sin nombre') ?></div>
                                        <div class="small">
                                            <a href="mailto:<?= htmlspecialchars($m['email'] ?? '') ?>" class="text-decoration-none text-muted">
                                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($m['email'] ?? '') ?>
                                            </a>
                                        </div>
                                        <?php if (!empty($m['phone'])): ?>
                                            <div class="small mt-1 d-flex align-items-center gap-2">
                                                <a href="tel:<?= htmlspecialchars($m['phone']) ?>" class="text-decoration-none text-dark">
                                                    <i class="bi bi-telephone me-1 text-primary"></i><?= htmlspecialchars($m['phone']) ?>
                                                </a>
                                                <?php if ($clean_phone): ?>
                                                    <a href="https://wa.me/<?= htmlspecialchars($clean_phone) ?>" target="_blank" class="btn btn-sm btn-outline-success py-0 px-1" style="font-size: 0.75rem;" title="Chatear en WhatsApp">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <?= htmlspecialchars($m['subject'] ?? ($m['tour'] ?? 'Consulta General')) ?>
                                        </div>
                                        <div class="text-muted small text-truncate" style="max-width: 380px;">
                                            <?= htmlspecialchars(mb_substr($m['message'] ?? '', 0, 120)) . (mb_strlen($m['message'] ?? '') > 120 ? '...' : '') ?>
                                        </div>
                                        <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                                            Ver mensaje completo →
                                        </button>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="mensajes.php?action=toggle_status&id=<?= urlencode($m['id']) ?>&current=<?= urlencode($m['status'] ?? 'nuevo') ?>&filter=<?= urlencode($active_filter) ?>" class="btn btn-outline-secondary" title="<?= $is_nuevo ? 'Marcar como leído' : 'Marcar como no leído' ?>">
                                                <i class="bi <?= $is_nuevo ? 'bi-check2' : 'bi-envelope' ?>"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>" title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="mensajes.php?action=delete&id=<?= urlencode($m['id']) ?>&filter=<?= urlencode($active_filter) ?>" class="btn btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este mensaje del registro?');" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detalle del Mensaje -->
                                <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" style="color: #0f2454;">
                                                    <i class="bi <?= ($m['type'] ?? '') === 'reserva' ? 'bi-calendar-check text-warning' : 'bi-envelope text-primary' ?> me-2"></i>
                                                    <?= ($m['type'] ?? '') === 'reserva' ? 'Solicitud de Reserva' : 'Mensaje de Contacto' ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="bg-light p-3 rounded mb-3">
                                                    <div class="row g-2 small">
                                                        <div class="col-6"><strong>Fecha:</strong> <?= htmlspecialchars($m['created_at'] ?? '') ?></div>
                                                        <div class="col-6"><strong>Estado:</strong> <?= ucfirst(htmlspecialchars($m['status'] ?? 'nuevo')) ?></div>
                                                        <div class="col-12"><strong>Cliente:</strong> <?= htmlspecialchars($m['name'] ?? '') ?></div>
                                                        <div class="col-12"><strong>Correo:</strong> <a href="mailto:<?= htmlspecialchars($m['email'] ?? '') ?>"><?= htmlspecialchars($m['email'] ?? '') ?></a></div>
                                                        <div class="col-12"><strong>Teléfono:</strong> <a href="tel:<?= htmlspecialchars($m['phone'] ?? '') ?>"><?= htmlspecialchars($m['phone'] ?? '') ?></a></div>
                                                        <?php if (!empty($m['tour'])): ?>
                                                            <div class="col-12"><strong>Tour solicitado:</strong> <?= htmlspecialchars($m['tour']) ?></div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($m['fecha'])): ?>
                                                            <div class="col-6"><strong>Fecha estimada:</strong> <?= htmlspecialchars($m['fecha']) ?></div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($m['people'])): ?>
                                                            <div class="col-6"><strong>Personas:</strong> <?= htmlspecialchars($m['people']) ?></div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($m['subject'])): ?>
                                                            <div class="col-12"><strong>Asunto / Motivo:</strong> <?= htmlspecialchars($m['subject']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <h6 class="fw-bold mb-2">Mensaje del cliente:</h6>
                                                <div class="p-3 bg-white border rounded" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">
                                                    <?= htmlspecialchars($m['message'] ?? 'Sin contenido') ?>
                                                </div>

                                                <?php if (!empty($m['ip'])): ?>
                                                    <div class="text-muted mt-2" style="font-size: 0.7rem;">
                                                        IP origen: <?= htmlspecialchars($m['ip']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between">
                                                <div>
                                                    <?php if ($clean_phone): ?>
                                                        <a href="https://wa.me/<?= htmlspecialchars($clean_phone) ?>" target="_blank" class="btn btn-success btn-sm">
                                                            <i class="bi bi-whatsapp me-1"></i> Responder por WhatsApp
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="mailto:<?= htmlspecialchars($m['email'] ?? '') ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-envelope me-1"></i> Responder por Email
                                                    </a>
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Buscador interactivo en vivo
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#messagesTable tbody tr.search-item');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
