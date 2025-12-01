<?php
session_start();
require_once 'includes/config.php';

// Vérifie que le client est connecté
if (empty($_SESSION['client_id'])) {
    header('Location: ' . PROJECT_ROOT . '/login.php');
    exit;
}

$client_id = (int)$_SESSION['client_id'];
$id_reservation = (int)($_GET['id'] ?? 0);

if ($id_reservation > 0) {
    // Vérifie que la réservation appartient bien au client
    $query_check = "SELECT id_chambre FROM reservation WHERE id_reservation = $1 AND id_client = $2";
    $res_check = pg_query_params($db, $query_check, [$id_reservation, $client_id]);

    if ($res_check && pg_num_rows($res_check) === 1) {
        $chambre = pg_fetch_assoc($res_check);

        // Supprime la réservation
        $query_delete = "DELETE FROM reservation WHERE id_reservation = $1";
        pg_query_params($db, $query_delete, [$id_reservation]);

        // Remet la chambre en libre
        pg_query_params($db, "UPDATE chambre SET statut='libre' WHERE id_chambre = $1", [$chambre['id_chambre']]);

        header('Location: ' . PROJECT_ROOT . '/client_dashboard.php?msg=' . urlencode('Réservation annulée.'));
        exit;
    } else {
        die("Réservation non trouvée ou non autorisée.");
    }
} else {
    die("ID réservation invalide.");
}
?>
