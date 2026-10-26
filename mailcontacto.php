<?php
// Desactivar visualización de errores en pantalla para producción (evita fuga de rutas e información interna)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Solo permitir solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// 1. Obtener y validar el token de Cloudflare Turnstile
$captcha = trim($_POST['cf-turnstile-response'] ?? '');

if (empty($captcha)) {
    http_response_code(400);
    exit('Por favor completa la verificación de seguridad (Captcha).');
}

$secretKey = "0x4AAAAAAAEh5oKSfOhZBhQy7rXq-oZP5LA";
$remoteIp  = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

$postData = [
    'secret'   => $secretKey,
    'response' => $captcha,
    'remoteip' => $remoteIp
];

$verifySuccess = false;

// Método principal: cURL (no depende de allow_url_fopen y funciona en servidores cPanel)
if (function_exists('curl_init')) {
    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (!empty($data['success'])) {
            $verifySuccess = true;
        }
    }
}

// Método fallback: file_get_contents (si cURL no estuviera disponible y allow_url_fopen está activo)
if (!$verifySuccess && ini_get('allow_url_fopen')) {
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($postData),
            'timeout' => 10
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (!empty($data['success'])) {
            $verifySuccess = true;
        }
    }
}

if (!$verifySuccess) {
    http_response_code(400);
    exit('La verificación de seguridad ha fallado o expiró. Por favor recarga la página e intenta de nuevo.');
}

// 2. Obtener y validar los campos del formulario
$name    = mb_substr(trim($_POST['name'] ?? ''), 0, 100);
$email   = mb_substr(trim($_POST['email'] ?? ''), 0, 150);
$phone   = mb_substr(trim($_POST['phone'] ?? ''), 0, 40);
$subject = mb_substr(trim($_POST['subject'] ?? ''), 0, 150);
$message = mb_substr(trim($_POST['message'] ?? ''), 0, 3000);

if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
    http_response_code(400);
    exit('Por favor completa todos los campos requeridos.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Por favor ingresa un correo electrónico válido.');
}

if (empty($_POST['terms'])) {
    http_response_code(400);
    exit('Debes aceptar los Términos y Condiciones.');
}

// 3. Sanitizar datos contra inyección HTML / Phishing en clientes de correo
$safe_name    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$safe_email   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$safe_phone   = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$safe_subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$safe_message = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

// Limpiar encabezados de email contra inyección CRLF
$clean_name  = str_replace(["\r", "\n"], '', $name);
$clean_email = str_replace(["\r", "\n"], '', $email);

// 4. Configurar destinatarios y cabeceras
$correo_destino = "gerencia@titicacaexperiencesperu.com";
$cc             = "titicacaexperiences@gmail.com";
$from           = "noreply@titicacaexperiencesperu.com";
$sign           = "Web Contáctanos - Titicaca Experiences";

$email_subject   = "Formulario Contáctanos: " . $subject;
$encoded_subject = "=?UTF-8?B?" . base64_encode($email_subject) . "?=";

$headers   = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-type: text/html; charset=UTF-8";
$headers[] = "From: {$sign} <{$from}>";
$headers[] = "Reply-To: {$clean_name} <{$clean_email}>";
if (!empty($cc)) {
    $headers[] = "Cc: {$cc}";
}
$headers[] = "X-Mailer: PHP/" . phpversion();

$email_content = "<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
        <h2 style='color: #2095AE; border-bottom: 2px solid #2095AE; padding-bottom: 8px;'>Nuevo Mensaje desde el Formulario de Contacto</h2>
        <p><strong>Nombre:</strong> {$safe_name}</p>
        <p><strong>Correo electrónico:</strong> <a href='mailto:{$safe_email}'>{$safe_email}</a></p>
        <p><strong>Teléfono:</strong> {$safe_phone}</p>
        <p><strong>Motivo / Asunto:</strong> {$safe_subject}</p>
        <p><strong>Mensaje:</strong></p>
        <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #2095AE; border-radius: 4px;'>
            {$safe_message}
        </div>
        <hr style='border: none; border-top: 1px solid #eee; margin-top: 25px;'>
        <p style='font-size: 12px; color: #888;'>Este correo fue enviado automáticamente desde el sitio web titicacaexperiencesperu.com</p>
    </div>
</body>
</html>";

$headers_str = implode("\r\n", $headers);
$enviado     = @mail($correo_destino, $encoded_subject, $email_content, $headers_str, "-f " . $from);

// Guardar respaldo seguro en el servidor (en data/messages.json)
require_once __DIR__ . '/includes/contact_helpers.php';
save_message([
    'type'       => 'contacto',
    'name'       => $name,
    'email'      => $email,
    'phone'      => $phone,
    'subject'    => $subject,
    'message'    => $message,
    'ip'         => $remoteIp,
    'email_sent' => $enviado ? true : false
]);

if ($enviado) {
    http_response_code(200);
    echo "Tu mensaje fue enviado satisfactoriamente. Nos comunicaremos contigo a la brevedad.";
} else {
    http_response_code(500);
    echo "No se pudo enviar el mensaje en este momento. Por favor contáctanos vía WhatsApp o teléfono.";
}
?>