<?php
require_once __DIR__ . "/../../includes/db.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_client = (int)$_GET['id'];
    $query = "DELETE FROM Client WHERE id_client=$id_client";

    if (executeQuery($query)) {
        // Redirection avec paramètre pour afficher un message dans la liste
        header("Location: liste.php?status=deleted");
        exit();
    } else {
        header("Location: liste.php?status=error");
        exit();
    }
} else {
    header("Location: liste.php");
    exit();
}
?>
