<?php
session_start();
require_once __DIR__ . '/../../includes/config.php'; // remonte de 2 niveaux pour inclure config

if(!isset($_SESSION['client_id'])) {
    header('Location: ' . PROJECT_ROOT . '/client_login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$client_id = $_SESSION['client_id'];

// Récupérer la réservation du client
$res_query = pg_query($db, "SELECT * FROM Reservation WHERE id_reservation=$id AND id_client=$client_id");
$res = pg_fetch_assoc($res_query);

if (!$res) die("Réservation introuvable.");

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    pg_query($db, "UPDATE Reservation SET date_debut='$date_debut', date_fin='$date_fin' WHERE id_reservation=$id AND id_client=$client_id");
    header('Location: ' . PROJECT_ROOT . '/client_dashboard.php'); // retour au dashboard
    exit;
}
?>

<form method="post" class="container my-5">
    <h3>Modifier la réservation #<?= $id ?></h3>
    <div class="mb-3">
        <label>Date début</label>
        <input type="date" name="date_debut" value="<?= htmlspecialchars($res['date_debut']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Date fin</label>
        <input type="date" name="date_fin" value="<?= htmlspecialchars($res['date_fin']) ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-gold">Modifier la réservation</button>
    <a href="<?= PROJECT_ROOT ?>/client_dashboard.php" class="btn btn-outline-light">Annuler</a>
</form>
