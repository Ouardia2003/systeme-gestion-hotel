<?php

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$_SESSION = [];
session_destroy();


header('Location: ' . PROJECT_ROOT . '/index.php?logout_client=success');
exit;
