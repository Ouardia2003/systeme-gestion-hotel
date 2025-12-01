<?php
// modifier_reservation.php
session_start();
require_once 'includes/config.php';

// Vérifie que le client est connecté
if (empty($_SESSION['client_id'])) {
    header('Location: index.php?error_client=' . urlencode('Veuillez vous connecter.'));
    exit;
}

$client_id = (int)$_SESSION['client_id'];
$reservation_id = (int)($_GET['id'] ?? 0);
$message = '';

// 1. Récupérer la réservation avec capacité max depuis Type_Chambre
$query_res = "
    SELECT r.*, t.capacite_max, c.id_chambre, t.nom AS type_nom
    FROM reservation r
    JOIN chambre c ON r.id_chambre = c.id_chambre
    JOIN type_chambre t ON c.id_type = t.id_type
    WHERE r.id_reservation = $1 AND r.id_client = $2
";
$params = [$reservation_id, $client_id];
$result_res = pg_query_params($db, $query_res, $params);

if (!$result_res || pg_num_rows($result_res) === 0) {
    die("Réservation non trouvée ou non autorisée.");
}

$reservation = pg_fetch_assoc($result_res);

// 2. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $personnes = (int)$_POST['personnes'];

    if ($date_debut >= $date_fin) {
        $message = '<div class="alert alert-warning">La date de départ doit être après la date d\'arrivée.</div>';
    } elseif ($personnes < 1 || $personnes > $reservation['capacite_max']) {
        $message = '<div class="alert alert-warning">Le nombre de personnes doit être entre 1 et ' . $reservation['capacite_max'] . '.</div>';
    } else {
        $sql_update = "
            UPDATE reservation
            SET date_debut = $1, date_fin = $2, nombre_personne = $3
            WHERE id_reservation = $4 AND id_client = $5
        ";
        $params_update = [$date_debut, $date_fin, $personnes, $reservation_id, $client_id];
        $res_update = pg_query_params($db, $sql_update, $params_update);

        if ($res_update) {
            $message = '<div class="alert alert-success">Réservation mise à jour avec succès !</div>';
            // recharger les nouvelles valeurs
            $reservation['date_debut'] = $date_debut;
            $reservation['date_fin'] = $date_fin;
            $reservation['nombre_personne'] = $personnes;
        } else {
            $message = '<div class="alert alert-danger">Erreur technique : ' . pg_last_error($db) . '</div>';
        }
    }
}

// 3. Supprimer la réservation
if (isset($_POST['supprimer'])) {
    $sql_delete = "DELETE FROM reservation WHERE id_reservation = $1 AND id_client = $2";
    $params_delete = [$reservation_id, $client_id];
    $res_delete = pg_query_params($db, $sql_delete, $params_delete);

    if ($res_delete) {
        header('Location: client_dashboard.php?msg=' . urlencode('Réservation annulée avec succès.'));
        exit;
    } else {
        $message = '<div class="alert alert-danger">Erreur lors de l\'annulation : ' . pg_last_error($db) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Réservation | HôtelSys</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f5f5f5; font-family: 'Roboto', sans-serif; }
.card { border-radius: 12px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 50px; }
.btn-gold { background-color: #c39d67; color: #fff; font-weight: bold; border: none; }
.btn-gold:hover { background-color: #b18859; }
</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <h3 class="text-center text-gold mb-4">Modifier votre réservation</h3>
                <?= $message ?>
                <div class="mb-3">
                    <strong>Chambre :</strong> <?= htmlspecialchars($reservation['type_nom']) ?><br>
                    <strong>Capacité max :</strong> <?= $reservation['capacite_max'] ?> personnes
                </div>
                <form method="post">
                    <div class="mb-3">
                        <label>Date d'arrivée</label>
                        <input type="date" name="date_debut" class="form-control" required
                               value="<?= htmlspecialchars($reservation['date_debut']) ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label>Date de départ</label>
                        <input type="date" name="date_fin" class="form-control" required
                               value="<?= htmlspecialchars($reservation['date_fin']) ?>"
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Nombre de personnes</label>
                        <input type="number" name="personnes" class="form-control"
                               value="<?= htmlspecialchars($reservation['nombre_personne']) ?>"
                               min="1" max="<?= $reservation['capacite_max'] ?>" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-gold">Modifier</button>
                        <button type="submit" name="supprimer" class="btn btn-danger">Annuler</button>
                    </div>
                </form>
                <a href="client_dashboard.php" class="btn btn-secondary mt-3">Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
