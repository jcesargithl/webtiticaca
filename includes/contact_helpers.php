<?php
// includes/contact_helpers.php - Gestor de mensajes y reservas de respaldo

/**
 * Retorna la ruta al archivo de almacenamiento JSON de mensajes.
 */
function get_messages_file() {
    return __DIR__ . '/../data/messages.json';
}

/**
 * Obtiene todos los mensajes registrados.
 * @return array
 */
function get_all_messages() {
    $file = get_messages_file();
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    if ($content === false || trim($content) === '') {
        return [];
    }
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

/**
 * Guarda una lista completa de mensajes.
 * @param array $messages
 * @return bool
 */
function save_all_messages(array $messages) {
    $file = get_messages_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $json = json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Registra un nuevo mensaje (contacto o reserva).
 * @param array $entry
 * @return array|false
 */
function save_message(array $entry) {
    // Configurar zona horaria de Perú
    if (function_exists('date_default_timezone_set')) {
        @date_default_timezone_set('America/Lima');
    }

    $id = uniqid('msg_', true);
    $now = date('Y-m-d H:i:s');

    $record = array_merge([
        'id'         => $id,
        'type'       => 'contacto',
        'created_at' => $now,
        'status'     => 'nuevo', // 'nuevo', 'leido', 'atendido'
        'email_sent' => false
    ], $entry);

    $messages = get_all_messages();
    // Añadir al inicio para que los más recientes aparezcan primero
    array_unshift($messages, $record);

    if (save_all_messages($messages)) {
        return $record;
    }
    return false;
}

/**
 * Actualiza el estado de un mensaje.
 * @param string $id
 * @param string $status ('nuevo', 'leido', 'atendido')
 * @return bool
 */
function update_message_status($id, $status = 'leido') {
    $messages = get_all_messages();
    $found = false;
    foreach ($messages as &$msg) {
        if ($msg['id'] === $id) {
            $msg['status'] = $status;
            $found = true;
            break;
        }
    }
    if ($found) {
        return save_all_messages($messages);
    }
    return false;
}

/**
 * Elimina un mensaje por su ID.
 * @param string $id
 * @return bool
 */
function delete_message($id) {
    $messages = get_all_messages();
    $initial_count = count($messages);
    $filtered = array_values(array_filter($messages, function($msg) use ($id) {
        return $msg['id'] !== $id;
    }));
    if (count($filtered) !== $initial_count) {
        return save_all_messages($filtered);
    }
    return false;
}

/**
 * Retorna la cantidad de mensajes no leídos.
 * @return int
 */
function get_unread_messages_count() {
    $messages = get_all_messages();
    $count = 0;
    foreach ($messages as $msg) {
        if (($msg['status'] ?? 'nuevo') === 'nuevo') {
            $count++;
        }
    }
    return $count;
}
