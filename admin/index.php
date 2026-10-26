<?php
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
// admin/index.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/blog_helpers.php';
require_once __DIR__ . '/../includes/contact_helpers.php';

$error = '';
$message = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'saved') {
        $message = 'Artículo guardado con éxito.';
    } elseif ($_GET['msg'] === 'deleted') {
        $message = 'Artículo eliminado con éxito.';
    }
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $password = $_POST['password'] ?? '';
    if (hash('sha256', $password) === ADMIN_PASSWORD_HASH) {
        $_SESSION['admin_logged'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Contraseña incorrecta. Inténtalo de nuevo.';
    }
}

$is_logged = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
$posts = $is_logged ? get_all_posts(true) : [];
$unread_count = $is_logged ? get_unread_contacts_count() : 0;
$unread_reservations = $is_logged ? get_unread_reservations_count() : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración - Blog Titicaca</title>
    <link rel="shortcut icon" href="../img/favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333;
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
        .login-card {
            max-width: 420px;
            margin: 80px auto;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border: none;
        }
        .post-card-thumb {
            width: 70px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }
        .navbar-custom {
            background: #0f2454;
        }
    </style>
</head>
<body>

<?php if (!$is_logged): ?>
    <!-- Pantalla de Login -->
    <div class="container">
        <div class="card login-card p-4 bg-white">
            <div class="text-center mb-4">
                <img src="../img/logo-titie.png" alt="Titicaca Experiences" style="max-height: 55px;" class="mb-2">
                <h5 class="fw-bold mt-2" style="color: #0f2454;">Administrador del Blog</h5>
                <p class="text-muted small">Ingresa tu contraseña para crear o editar artículos</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="login">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Contraseña</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required autofocus>
                </div>
                <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold">Ingresar al Panel</button>
            </form>
            <div class="text-center mt-4">
                <a href="../blog.php" class="text-muted small text-decoration-none">← Volver al sitio web</a>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Panel de Administración -->
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
                        <a class="nav-link px-3 py-1 active bg-white text-dark fw-bold rounded" href="index.php">
                            <i class="bi bi-newspaper me-1 text-primary"></i> Blog
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
                    <a href="../galeria.php" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-eye me-1"></i> Ver Galería en Vivo
                    </a>
                    <a href="../blog.php" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Ver Blog en Vivo
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

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1" style="color: #0f2454;">Publicaciones del Blog</h3>
                <p class="text-muted mb-0">Total: <strong><?= count($posts) ?></strong> artículos publicados</p>
            </div>
            <a href="edit.php" class="btn btn-brand px-3 py-2 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Artículo
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Foto</th>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th>Autor</th>
                            <th class="text-end" style="min-width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No hay artículos registrados aún. ¡Crea el primero con el botón "+ Nuevo Artículo"!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $p): 
                                $thumb = !empty($p['image_thumb']) ? $p['image_thumb'] : (!empty($p['image_cover']) ? $p['image_cover'] : '../img/slider/slidepost.jpg');
                                $post_link = '../post.php?slug=' . urlencode($p['slug']);
                            ?>
                                <tr>
                                    <td>
                                        <img src="../<?= htmlspecialchars($thumb) ?>" class="post-card-thumb border" alt="">
                                    </td>
                                    <td>
                                        <a href="<?= $post_link ?>" target="_blank" class="fw-bold text-dark text-decoration-none">
                                            <?= htmlspecialchars($p['title']) ?>
                                        </a>
                                        <?php if (!empty($p['quote'])): ?>
                                            <span class="badge bg-light text-primary border ms-1" style="font-size: 11px;">
                                                <i class="bi bi-quote"></i> Con Cita
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($p['category'] ?? 'General') ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($p['date'] ?? '') ?>
                                    </td>
                                    <td class="small">
                                        <?= htmlspecialchars($p['author'] ?? 'Ernesto Aliaga') ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= $post_link ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="Ver en la web">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= urlencode($p['id']) ?>" class="btn btn-sm btn-primary me-1" title="Editar artículo">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        <a href="delete.php?id=<?= urlencode($p['id']) ?>" class="btn btn-sm btn-outline-danger" title="Eliminar artículo" onclick="return confirm('¿Estás seguro de que deseas eliminar este artículo? Esta acción no se puede deshacer.');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 p-3 bg-white rounded border small text-muted">
            <i class="bi bi-info-circle text-primary me-1"></i>
            <strong>Nota para el usuario:</strong> Cualquier cambio o nuevo post que publiques aquí se reflejará al instante en la sección de blog de la página web. No necesitas tocar código ni bases de datos.
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
