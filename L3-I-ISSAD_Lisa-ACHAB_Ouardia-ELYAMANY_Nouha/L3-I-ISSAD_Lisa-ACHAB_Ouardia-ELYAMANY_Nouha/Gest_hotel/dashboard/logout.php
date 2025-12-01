<?php
// Inclure le fichier config avec le chemin correct
require_once __DIR__ . '/../includes/config.php';

session_start();

// Détruire toutes les variables de session
session_unset();
session_destroy();

// Rediriger vers la page login avec message de déconnexion
header('Location: ../login.php?logout=1');
exit;
?>
