<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php?error=unauthorized");
    exit;
}
if (time() - ($_SESSION['last_activity'] ?? 0) > 3600) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=session_expired");
    exit;
}

$_SESSION['last_activity'] = time();
?>
