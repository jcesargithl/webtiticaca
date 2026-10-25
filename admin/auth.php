<?php
// admin/auth.php
require_once __DIR__ . '/config.php';

function is_admin_logged() {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function require_admin_login() {
    if (!is_admin_logged()) {
        header('Location: index.php');
        exit;
    }
}
