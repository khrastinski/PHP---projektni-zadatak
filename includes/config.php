<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/Zabocki-lavovi');

require_once __DIR__ . '/auth.php';