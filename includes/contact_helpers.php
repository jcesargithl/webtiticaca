<?php
// includes/contact_helpers.php - Gestor de mensajes y reservas de respaldo

/**
 * Retorna la ruta al archivo de almacenamiento JSON de mensajes y reservas.
 */
function get_messages_file() {
    return __DIR__ . '/../data/messages.json';
}

/**
 * Obtiene todos los registros almacenados (contactos y reservas).
 * @return array
 */
function get_all_messages() {
    $file = get_messages_file();
    if (!file_exists($file)) {
        return [];
    }
    clearstatcache(true, $file);
    $content = file_get_contents($file);
    if ($content === false || trim($content) === '') {
        return [];
    }
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

/**
 * Guarda una lista completa de mensajes/reservas.
 * @param array $messages
 * @return bool
 */
function save_all_messages(array $messages) {
    $file = get_messages_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $json = json_encode(array_values($messages), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $bytes = file_put_contents($file, $json, LOCK_EX);
    if ($bytes !== false) {
        @chmod($file, 0666);
        clearstatcache(true, $file);
        return true;
    }
    return false;
}

/**
 * Registra un nuevo mensaje o solicitud de reserva.
 * @param array $entry
 * @return array|false
 */
function save_message(array $entry) {
    if (function_exists('date_default_timezone_set')) {
        @date_default_timezone_set('America/Lima');
    }

    $type = $entry['type'] ?? 'contacto';
    $id = uniqid(($type === 'reserva' ? 'res_' : 'msg_'), true);
    $now = date('Y-m-d H:i:s');

    $record = array_merge([
        'id'         => $id,
        'type'       => $type,
        'created_at' => $now,
        'status'     => ($type === 'reserva' ? 'pendiente' : 'nuevo'), // 'pendiente'/'confirmada'/'cancelada' o 'nuevo'/'leido'
        'email_sent' => false
    ], $entry);

    $messages = get_all_messages();
    array_unshift($messages, $record);

    if (save_all_messages($messages)) {
        return $record;
    }
    return false;
}

/**
 * Actualiza el estado de un registro.
 * @param string $id
 * @param string $status
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
 * Elimina un registro por su ID.
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
 * Retorna solo las solicitudes de reserva de tours.
 * @return array
 */
function get_tour_reservations() {
    $all = get_all_messages();
    return array_values(array_filter($all, function($m) {
        return ($m['type'] ?? '') === 'reserva';
    }));
}

/**
 * Retorna solo los mensajes generales de contacto.
 * @return array
 */
function get_contact_inquiries() {
    $all = get_all_messages();
    return array_values(array_filter($all, function($m) {
        return ($m['type'] ?? 'contacto') === 'contacto';
    }));
}

/**
 * Retorna la cantidad de reservas pendientes de atención.
 * @return int
 */
function get_unread_reservations_count() {
    $reservations = get_tour_reservations();
    $count = 0;
    foreach ($reservations as $r) {
        $st = $r['status'] ?? 'pendiente';
        if ($st === 'pendiente' || $st === 'nuevo') {
            $count++;
        }
    }
    return $count;
}

/**
 * Retorna la cantidad de mensajes de contacto no leídos.
 * @return int
 */
function get_unread_contacts_count() {
    $contacts = get_contact_inquiries();
    $count = 0;
    foreach ($contacts as $c) {
        if (($c['status'] ?? 'nuevo') === 'nuevo') {
            $count++;
        }
    }
    return $count;
}

/**
 * Retorna el total combinado de mensajes y reservas no atendidos.
 * @return int
 */
function get_unread_messages_count() {
    return get_unread_contacts_count() + get_unread_reservations_count();
}
