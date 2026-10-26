<?php
// admin/reservas.php - Panel de Gestión de Reservas de Tours
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/contact_helpers.php';

require_admin_login();

// 1. Exportar reservas a CSV para Excel
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    $reservations = get_tour_reservations();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reservas_tours_titicaca_' . date('Ymd_His') . '.csv');
    $out = fopen('php://output', 'w');
    // BOM UTF-8 para Excel
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($out, [
        'ID', 
        'Fecha Solicitud', 
        'Tour', 
        'Fecha del Viaje', 
        'Pasajeros', 
        'Estado', 
        'Cliente', 
        'Correo', 
        'Teléfono', 
        'Consulta / Requerimientos', 
        'IP Origen', 
        'Email Despachado'
    ]);
    foreach ($reservations as $r) {
        fputcsv($out, [
            $r['id'] ?? '',
            $r['created_at'] ?? '',
            $r['tour'] ?? 'Tour General',
            $r['fecha'] ?? '',
            $r['people'] ?? '',
            ucfirst($r['status'] ?? 'pendiente'),
            $r['name'] ?? '',
            $r['email'] ?? '',
            $r['phone'] ?? '',
            $r['message'] ?? '',
            $r['ip'] ?? '',
            !empty($r['email_sent']) ? 'Sí' : 'No'
        ]);
    }
    fclose($out);
    exit;
}

// 2. Cambiar estado de la reserva (pendiente, confirmada, cancelada)
if (isset($_GET['action']) && $_GET['action'] === 'set_status') {
    $id = trim($_GET['id'] ?? '');
    $status = trim($_GET['status'] ?? 'pendiente');
    $allowed = ['pendiente', 'confirmada', 'cancelada'];
    if ($id !== '' && in_array($status, $allowed, true)) {
        update_message_status($id, $status);
    }
    $filter_param = isset($_GET['filter']) ? '?filter=' . urlencode($_GET['filter']) : '';
    header('Location: reservas.php' . $filter_param);
    exit;
}

// 3. Eliminar reserva
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = trim($_GET['id'] ?? '');
    if ($id !== '') {
        delete_message($id);
    }
    $filter_param = isset($_GET['filter']) ? '&filter=' . urlencode($_GET['filter']) : '';
    header('Location: reservas.php?msg=deleted' . $filter_param);
    exit;
}

$all_reservations = get_tour_reservations();
$unread_reservations = get_unread_reservations_count();
$unread_contacts     = get_unread_contacts_count();

// Estadísticas de reservas
$total_count     = count($all_reservations);
$pendientes_count = 0;
$confirmadas_count = 0;
$canceladas_count  = 0;
$tours_list        = [];

foreach ($all_reservations as $r) {
    $st = $r['status'] ?? 'pendiente';
    if ($st === 'confirmada') {
        $confirmadas_count++;
    } elseif ($st === 'cancelada') {
        $canceladas_count++;
    } else {
        $pendientes_count++;
    }

    $t_name = trim($r['tour'] ?? '');
    if ($t_name !== '' && !in_array($t_name, $tours_list, true)) {
        $tours_list[] = $t_name;
    }
}
sort($tours_list);

// Filtros
$active_filter = $_GET['filter'] ?? 'todos';
$tour_filter   = $_GET['tour'] ?? '';

$filtered_reservations = [];
foreach ($all_reservations as $r) {
    $st = $r['status'] ?? 'pendiente';
    
    // Filtro por estado
    $match_status = true;
    if ($active_filter === 'pendientes') {
        $match_status = ($st === 'pendiente' || $st === 'nuevo');
    } elseif ($active_filter === 'confirmadas') {
        $match_status = ($st === 'confirmada');
    } elseif ($active_filter === 'canceladas') {
        $match_status = ($st === 'cancelada');
    }

    // Filtro por tour
    $match_tour = true;
    if ($tour_filter !== '') {
        $match_tour = (($r['tour'] ?? '') === $tour_filter);
    }

    if ($match_status && $match_tour) {
        $filtered_reservations[] = $r;
    }
}

$message = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Reserva eliminada del registro.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Reservas de Tours - Admin Titicaca</title>
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
        .reserva-row-pendiente {
            background-color: #fffdf5 !important;
        }
        .badge-tour {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
            font-size: 0.85rem;
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
                        <a class="nav-link px-3 py-1 text-white-50" href="mensajes.php">
                            <i class="bi bi-chat-dots me-1"></i> Contactos
                            <?php if ($unread_contacts > 0): ?>
                                <span class="badge bg-secondary rounded-pill ms-1"><?= $unread_contacts ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-1 active bg-white text-dark fw-bold rounded" href="reservas.php">
                            <i class="bi bi-calendar-check me-1 text-primary"></i> Reservas
                            <?php if ($unread_reservations > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?= $unread_reservations ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a href="../tours.html" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-compass me-1"></i> Ver Tours en Vivo
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
                <h3 class="fw-bold mb-1" style="color: #0f2454;">
                    <i class="bi bi-calendar2-week me-2 text-primary"></i>Gestión de Reservas de Tours
                </h3>
                <p class="text-muted small mb-0">Listado y seguimiento de clientes interesados en los paquetes turísticos</p>
            </div>
            <div>
                <a href="reservas.php?action=export_csv" class="btn btn-success fw-semibold">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Descargar Excel de Reservas
                </a>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="reservas.php?filter=todos" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'todos' ? 'border border-primary' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Total Solicitudes</span>
                                <h3 class="fw-bold mb-0 text-dark"><?= $total_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-light text-primary fs-4">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="reservas.php?filter=pendientes" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'pendientes' ? 'border border-warning' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Pendientes</span>
                                <h3 class="fw-bold mb-0 text-warning-emphasis"><?= $pendientes_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-warning-subtle text-warning fs-4">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="reservas.php?filter=confirmadas" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'confirmadas' ? 'border border-success' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Confirmadas</span>
                                <h3 class="fw-bold mb-0 text-success"><?= $confirmadas_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-success-subtle text-success fs-4">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="reservas.php?filter=canceladas" class="text-decoration-none">
                    <div class="card stat-card p-3 bg-white <?= $active_filter === 'canceladas' ? 'border border-danger' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-semibold">Canceladas</span>
                                <h3 class="fw-bold mb-0 text-danger"><?= $canceladas_count ?></h3>
                            </div>
                            <div class="rounded-circle p-2 bg-danger-subtle text-danger fs-4">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Filtros, Selector de Tour y Buscador -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <ul class="nav nav-pills gap-1">
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'todos' ? 'active' : '' ?>" href="reservas.php?filter=todos<?= $tour_filter ? '&tour='.urlencode($tour_filter) : '' ?>">Todas (<?= $total_count ?>)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'pendientes' ? 'active bg-warning text-dark' : '' ?>" href="reservas.php?filter=pendientes<?= $tour_filter ? '&tour='.urlencode($tour_filter) : '' ?>">
                                    Pendientes (<?= $pendientes_count ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'confirmadas' ? 'active bg-success' : '' ?>" href="reservas.php?filter=confirmadas<?= $tour_filter ? '&tour='.urlencode($tour_filter) : '' ?>">
                                    Confirmadas (<?= $confirmadas_count ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 px-3 small <?= $active_filter === 'canceladas' ? 'active bg-danger' : '' ?>" href="reservas.php?filter=canceladas<?= $tour_filter ? '&tour='.urlencode($tour_filter) : '' ?>">
                                    Canceladas (<?= $canceladas_count ?>)
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-12 col-md-3">
                        <select class="form-select form-select-sm" onchange="location = this.value;">
                            <option value="reservas.php?filter=<?= urlencode($active_filter) ?>">Todos los Tours (<?= count($tours_list) ?>)</option>
                            <?php foreach ($tours_list as $t): ?>
                                <option value="reservas.php?filter=<?= urlencode($active_filter) ?>&tour=<?= urlencode($t) ?>" <?= $tour_filter === $t ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por cliente, tour, fecha...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Reservas -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reservationsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">Solicitud</th>
                            <th style="width: 220px;">Tour</th>
                            <th style="width: 150px;">Viaje / Pasajeros</th>
                            <th style="width: 230px;">Cliente</th>
                            <th style="width: 130px;">Estado</th>
                            <th style="width: 130px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_reservations)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                    No hay reservas registradas con los filtros actuales.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_reservations as $idx => $r): 
                                $status = $r['status'] ?? 'pendiente';
                                $is_pendiente = ($status === 'pendiente' || $status === 'nuevo');
                                $clean_phone = preg_replace('/[^0-9]/', '', $r['phone'] ?? '');
                                $modal_id = 'modalRes' . $idx;
                            ?>
                                <tr class="<?= $is_pendiente ? 'reserva-row-pendiente' : '' ?> search-item">
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($r['created_at'] ?? 'now'))) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars(date('H:i', strtotime($r['created_at'] ?? 'now'))) ?> hrs
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-tour px-2 py-1 rounded">
                                            <i class="bi bi-compass me-1"></i><?= htmlspecialchars($r['tour'] ?? 'Tour General') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <i class="bi bi-calendar3 me-1 text-primary"></i>
                                            <?= htmlspecialchars($r['fecha'] ?? 'Por definir') ?>
                                        </div>
                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-people me-1"></i><?= htmlspecialchars($r['people'] ?? '1') ?> persona(s)
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($r['name'] ?? 'Sin nombre') ?></div>
                                        <div class="small">
                                            <a href="mailto:<?= htmlspecialchars($r['email'] ?? '') ?>" class="text-decoration-none text-muted">
                                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($r['email'] ?? '') ?>
                                            </a>
                                        </div>
                                        <?php if (!empty($r['phone'])): ?>
                                            <div class="small mt-1 d-flex align-items-center gap-2">
                                                <a href="tel:<?= htmlspecialchars($r['phone']) ?>" class="text-decoration-none text-dark">
                                                    <i class="bi bi-telephone me-1 text-primary"></i><?= htmlspecialchars($r['phone']) ?>
                                                </a>
                                                <?php if ($clean_phone): ?>
                                                    <a href="https://wa.me/<?= htmlspecialchars($clean_phone) ?>?text=<?= urlencode('Hola ' . ($r['name'] ?? '') . ', te saludamos de Titicaca Experiences sobre tu reserva del ' . ($r['tour'] ?? 'Tour') . '.') ?>" target="_blank" class="btn btn-sm btn-outline-success py-0 px-1" style="font-size: 0.75rem;" title="Abrir WhatsApp">
                                                        <i class="bi bi-whatsapp"></i> WhatsApp
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <?php if ($status === 'confirmada'): ?>
                                                <button class="btn btn-sm btn-success dropdown-toggle py-0 px-2" style="font-size: 0.75rem;" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-check-circle me-1"></i>Confirmada
                                                </button>
                                            <?php elseif ($status === 'cancelada'): ?>
                                                <button class="btn btn-sm btn-danger dropdown-toggle py-0 px-2" style="font-size: 0.75rem;" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-x-circle me-1"></i>Cancelada
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-warning text-dark dropdown-toggle py-0 px-2" style="font-size: 0.75rem;" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-hourglass-split me-1"></i>Pendiente
                                                </button>
                                            <?php endif; ?>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.85rem;">
                                                <li><a class="dropdown-item" href="reservas.php?action=set_status&id=<?= urlencode($r['id']) ?>&status=pendiente&filter=<?= urlencode($active_filter) ?>"><i class="bi bi-hourglass me-2 text-warning"></i>Marcar Pendiente</a></li>
                                                <li><a class="dropdown-item" href="reservas.php?action=set_status&id=<?= urlencode($r['id']) ?>&status=confirmada&filter=<?= urlencode($active_filter) ?>"><i class="bi bi-check-circle me-2 text-success"></i>Marcar Confirmada</a></li>
                                                <li><a class="dropdown-item" href="reservas.php?action=set_status&id=<?= urlencode($r['id']) ?>&status=cancelada&filter=<?= urlencode($active_filter) ?>"><i class="bi bi-x-circle me-2 text-danger"></i>Marcar Cancelada</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>" title="Ver ficha de reserva">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="reservas.php?action=delete&id=<?= urlencode($r['id']) ?>&filter=<?= urlencode($active_filter) ?>" class="btn btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar esta reserva del registro?');" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detalle de la Reserva -->
                                <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold" style="color: #0f2454;">
                                                    <i class="bi bi-calendar-check text-primary me-2"></i>Ficha de Reserva
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="p-3 bg-light rounded mb-3">
                                                    <div class="row g-2 small">
                                                        <div class="col-12">
                                                            <span class="text-muted">Tour solicitado:</span>
                                                            <div class="fw-bold fs-6 text-primary"><?= htmlspecialchars($r['tour'] ?? 'Tour General') ?></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted">Fecha del viaje:</span>
                                                            <div class="fw-bold"><?= htmlspecialchars($r['fecha'] ?? 'Por coordinar') ?></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted">Pasajeros:</span>
                                                            <div class="fw-bold"><?= htmlspecialchars($r['people'] ?? '1') ?> persona(s)</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted">Solicitado el:</span>
                                                            <div><?= htmlspecialchars($r['created_at'] ?? '') ?></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <span class="text-muted">Estado actual:</span>
                                                            <div class="fw-bold text-uppercase"><?= htmlspecialchars($status) ?></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h6 class="fw-bold mb-2">Datos del Cliente:</h6>
                                                <ul class="list-group list-group-flush small mb-3 border rounded">
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span class="text-muted">Nombre:</span>
                                                        <span class="fw-semibold"><?= htmlspecialchars($r['name'] ?? '') ?></span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span class="text-muted">Email:</span>
                                                        <a href="mailto:<?= htmlspecialchars($r['email'] ?? '') ?>"><?= htmlspecialchars($r['email'] ?? '') ?></a>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span class="text-muted">Teléfono:</span>
                                                        <a href="tel:<?= htmlspecialchars($r['phone'] ?? '') ?>"><?= htmlspecialchars($r['phone'] ?? '') ?></a>
                                                    </li>
                                                </ul>

                                                <h6 class="fw-bold mb-2">Consulta / Requerimientos:</h6>
                                                <div class="p-3 bg-white border rounded small" style="white-space: pre-wrap; line-height: 1.6;">
                                                    <?= htmlspecialchars($r['message'] ?: 'Sin comentarios adicionales.') ?>
                                                </div>

                                                <?php if (!empty($r['ip'])): ?>
                                                    <div class="text-muted mt-2" style="font-size: 0.7rem;">
                                                        IP origen: <?= htmlspecialchars($r['ip']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between">
                                                <div>
                                                    <?php if ($clean_phone): ?>
                                                        <a href="https://wa.me/<?= htmlspecialchars($clean_phone) ?>?text=<?= urlencode('Hola ' . ($r['name'] ?? '') . ', te contactamos de Titicaca Experiences respecto a tu reserva de ' . ($r['tour'] ?? 'Tour') . '.') ?>" target="_blank" class="btn btn-success btn-sm">
                                                            <i class="bi bi-whatsapp me-1"></i> WhatsApp
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="mailto:<?= htmlspecialchars($r['email'] ?? '') ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-envelope me-1"></i> Email
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
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#reservationsTable tbody tr.search-item');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
