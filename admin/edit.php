<?php
// admin/edit.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/blog_helpers.php';

require_admin_login();

$error = '';
$post_id = isset($_GET['id']) ? trim($_GET['id']) : '';
$is_edit = ($post_id !== '' && $post_id !== 'new');

$all_posts = get_all_posts(false);
$post = null;

if ($is_edit) {
    foreach ($all_posts as $p) {
        if ((string)$p['id'] === $post_id || $p['slug'] === $post_id) {
            $post = $p;
            break;
        }
    }
    if (!$post) {
        header('Location: index.php');
        exit;
    }
}

// Valores por defecto
$title = $post['title'] ?? '';
$category = $post['category'] ?? 'Experiencia';
$author = $post['author'] ?? 'Ernesto Aliaga';
$date = $post['date'] ?? date('Y-m-d');
$tags = isset($post['tags']) && is_array($post['tags']) ? implode(', ', $post['tags']) : 'Viaje, Puno, Turismo';
$quote = $post['quote'] ?? '';
$quote_author = $post['quote_author'] ?? 'Tito Aliaga';
$content = $post['content'] ?? '<p>Escribe aquí el contenido de tu publicación...</p>';
$current_image = $post['image_cover'] ?? ($post['image_thumb'] ?? '');

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $author = trim($_POST['author'] ?? 'Ernesto Aliaga');
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    $tags_raw = trim($_POST['tags'] ?? '');
    $quote = trim($_POST['quote'] ?? '');
    $quote_author = trim($_POST['quote_author'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '') {
        $error = 'Por favor ingresa un título para la publicación.';
    } elseif ($content === '' || $content === '<p><br></p>') {
        $error = 'Por favor escribe el contenido de la publicación.';
    } else {
        // Convertir tags en array limpio
        $tags_array = [];
        if ($tags_raw !== '') {
            $parts = explode(',', $tags_raw);
            foreach ($parts as $t) {
                $t = trim($t);
                if ($t !== '') {
                    $tags_array[] = $t;
                }
            }
        }

        // Manejar subida de imagen
        $saved_image_path = $current_image;
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = $_FILES['image_file']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($file_ext, $allowed_exts)) {
                $error = 'Formato de imagen no permitido. Usa JPG, PNG o WebP.';
            } else {
                $upload_dir = __DIR__ . '/../img/blog/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $new_file_name = 'blog_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $target_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    $saved_image_path = 'img/blog/uploads/' . $new_file_name;
                } else {
                    $error = 'Hubo un error al guardar la imagen en el servidor.';
                }
            }
        }

        if (!$error) {
            $date_parts = get_spanish_date_parts($date);

            if ($is_edit) {
                // Actualizar post existente
                foreach ($all_posts as &$p) {
                    if ((string)$p['id'] === (string)$post['id']) {
                        $p['title'] = $title;
                        $p['category'] = $category;
                        $p['author'] = $author;
                        $p['date'] = $date;
                        $p['display_date'] = $date_parts['full'];
                        $p['tags'] = $tags_array;
                        $p['quote'] = $quote;
                        $p['quote_author'] = $quote_author;
                        $p['content'] = $content;
                        if ($saved_image_path !== '') {
                            $p['image_cover'] = $saved_image_path;
                            $p['image_thumb'] = $saved_image_path;
                        }
                        break;
                    }
                }
            } else {
                // Crear nuevo post
                $new_id = 1;
                foreach ($all_posts as $p) {
                    if (isset($p['id']) && (int)$p['id'] >= $new_id) {
                        $new_id = (int)$p['id'] + 1;
                    }
                }

                $new_slug = slugify($title);
                // Verificar que el slug sea único
                $slug_base = $new_slug;
                $counter = 1;
                while (get_post_by_slug($new_slug) !== null) {
                    $new_slug = $slug_base . '-' . $counter;
                    $counter++;
                }

                $new_post = [
                    'id' => $new_id,
                    'slug' => $new_slug,
                    'title' => $title,
                    'category' => $category,
                    'tags' => $tags_array,
                    'author' => $author,
                    'author_avatar' => 'img/team/perfil.png',
                    'date' => $date,
                    'display_date' => $date_parts['full'],
                    'image_thumb' => $saved_image_path ?: 'img/slider/slidepost.jpg',
                    'image_cover' => $saved_image_path ?: 'img/slider/slidepost.jpg',
                    'quote' => $quote,
                    'quote_author' => $quote_author,
                    'content' => $content
                ];

                // Agregar al inicio para que quede como el más reciente
                array_unshift($all_posts, $new_post);
            }

            save_posts($all_posts);
            header('Location: index.php?msg=saved');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $is_edit ? 'Editar Artículo' : 'Nuevo Artículo' ?> - Titicaca Experiences</title>
    <link rel="shortcut icon" href="../img/favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Quill WYSIWYG Editor (Editor visual estilo Word) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333;
        }
        .navbar-custom {
            background: #0f2454;
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
        .card-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 18px rgba(0,0,0,0.06);
        }
        .quote-box-preview {
            background: #f8fafc;
            border-left: 4px solid #2095AE;
            border-radius: 6px;
            padding: 15px;
        }
        #editor-container {
            height: 350px;
            background: #fff;
            font-size: 16px;
        }
        .ql-toolbar {
            background: #fafbfc;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }
        .ql-container {
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
    </style>
</head>
<body>

    <!-- Barra Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-3 py-2 mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="../img/favicon.png" alt="" width="26" height="26" class="me-2">
                <span class="fw-bold">Admin Titicaca</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Blog
                </a>
                <a href="galeria.php" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-images me-1"></i> Ir a Galería
                </a>
            </div>
        </div>
    </nav>

    <div class="container mb-5" style="max-width: 920px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0" style="color: #0f2454;">
                <i class="bi <?= $is_edit ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-2"></i>
                <?= $is_edit ? 'Editar Publicación' : 'Crear Nueva Publicación' ?>
            </h3>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">Cancelar</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 mb-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="postForm">
            <!-- 1. Datos Principales -->
            <div class="card card-custom p-4 mb-4 bg-white">
                <h5 class="fw-bold mb-3" style="color: #0f2454;"><i class="bi bi-card-heading me-2"></i> Información Principal</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Título del artículo *</label>
                    <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($title) ?>" placeholder="Ej: Lugares mágicos que debes visitar en el Lago Titicaca" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Categoría *</label>
                        <select name="category" class="form-select">
                            <option value="Experiencia" <?= $category === 'Experiencia' ? 'selected' : '' ?>>Experiencia</option>
                            <option value="Deporte" <?= $category === 'Deporte' ? 'selected' : '' ?>>Deporte</option>
                            <option value="Tour" <?= $category === 'Tour' ? 'selected' : '' ?>>Tour</option>
                            <option value="Viajes" <?= $category === 'Viajes' ? 'selected' : '' ?>>Viajes</option>
                            <option value="Destinos" <?= $category === 'Destinos' ? 'selected' : '' ?>>Destinos</option>
                            <option value="Cultura" <?= $category === 'Cultura' ? 'selected' : '' ?>>Cultura</option>
                            <option value="Naturaleza" <?= $category === 'Naturaleza' ? 'selected' : '' ?>>Naturaleza</option>
                        </select>
                        <small class="text-muted">Selecciona la categoría a la que pertenece.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de publicación *</label>
                        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Autor *</label>
                        <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($author) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Etiquetas / Tags</label>
                        <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($tags) ?>" placeholder="Viaje, Tours, Aves, Puno">
                        <small class="text-muted">Palabras clave separadas por coma.</small>
                    </div>
                </div>
            </div>

            <!-- 2. Imagen de Portada -->
            <div class="card card-custom p-4 mb-4 bg-white">
                <h5 class="fw-bold mb-3" style="color: #0f2454;"><i class="bi bi-image me-2"></i> Foto de Portada</h5>
                
                <div class="row align-items-center">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <?php if ($current_image): ?>
                            <img src="../<?= htmlspecialchars($current_image) ?>" id="previewImg" class="img-fluid rounded border shadow-sm" style="max-height: 140px;" alt="Portada actual">
                            <div class="small text-muted mt-1">Foto actual</div>
                        <?php else: ?>
                            <div id="noImgText" class="p-4 bg-light text-muted border rounded">Sin imagen aún</div>
                            <img src="" id="previewImg" class="img-fluid rounded border shadow-sm d-none" style="max-height: 140px;" alt="Vista previa">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Seleccionar nueva foto</label>
                        <input type="file" name="image_file" id="imageInput" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-2">
                            Recomendado: imagen horizontal en formato JPG, PNG o WebP. Se subirá automáticamente al servidor.
                        </small>
                    </div>
                </div>
            </div>

            <!-- 3. Frase Destacada / Cita (Quote - Opcional) -->
            <div class="card card-custom p-4 mb-4 bg-white">
                <h5 class="fw-bold mb-2" style="color: #0f2454;"><i class="bi bi-quote me-2"></i> Frase Destacada / Cita (Opcional)</h5>
                <p class="text-muted small mb-3">Si escribes una frase aquí, se mostrará dentro de la elegante tarjeta con comillas y fondo gris de la web.</p>

                <div class="quote-box-preview">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Texto de la frase destacada</label>
                        <textarea name="quote" class="form-control" rows="2" placeholder="Ej: La naturaleza y la cultura jamás habían estado tan perfectamente entrelazadas como en el Lago Titicaca."><?= htmlspecialchars($quote) ?></textarea>
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Autor de la frase</label>
                        <input type="text" name="quote_author" class="form-control" value="<?= htmlspecialchars($quote_author) ?>" placeholder="Ej: Tito Aliaga">
                    </div>
                </div>
            </div>

            <!-- 4. Contenido del Artículo (Editor Visual Tipo Word) -->
            <div class="card card-custom p-4 mb-4 bg-white">
                <h5 class="fw-bold mb-2" style="color: #0f2454;"><i class="bi bi-pencil me-2"></i> Contenido del Artículo *</h5>
                <p class="text-muted small mb-3">Usa la barra de herramientas para poner negrita, subtítulos o listas tal como en Word.</p>

                <!-- Contenedor Quill -->
                <div id="editor-container"><?= $content ?></div>
                <input type="hidden" name="content" id="content_input">
            </div>

            <!-- Botón Guardar -->
            <div class="d-flex justify-content-between align-items-center p-3 bg-white card-custom">
                <a href="index.php" class="btn btn-outline-secondary px-4">Volver</a>
                <button type="submit" class="btn btn-brand btn-lg px-5 fw-bold">
                    <i class="bi bi-check-lg me-1"></i> <?= $is_edit ? 'Guardar Cambios' : 'Publicar Artículo' ?>
                </button>
            </div>
        </form>

    </div>

    <!-- Scripts Quill -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        // Inicializar editor visual Quill con barra tipo Word
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Escribe aquí la historia o información del tour...',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'link', 'image'],
                    ['clean']
                ]
            }
        });

        // Al enviar el formulario, pasar el HTML generado al input oculto
        const form = document.getElementById('postForm');
        form.onsubmit = function() {
            const contentInput = document.getElementById('content_input');
            contentInput.value = quill.root.innerHTML;
        };

        // Vista previa inmediata de la imagen seleccionada
        const imgInput = document.getElementById('imageInput');
        const previewImg = document.getElementById('previewImg');
        const noImgText = document.getElementById('noImgText');

        if (imgInput) {
            imgInput.addEventListener('change', function(e) {
                const [file] = imgInput.files;
                if (file) {
                    previewImg.src = URL.createObjectURL(file);
                    previewImg.classList.remove('d-none');
                    if (noImgText) noImgText.classList.add('d-none');
                }
            });
        }
    </script>
</body>
</html>
