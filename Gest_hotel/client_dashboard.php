<?php
session_start();
require_once 'includes/config.php';

// Vérifie que le client est connecté
if (empty($_SESSION['client_id'])) {
    header('Location: ' . PROJECT_ROOT . '/login.php');
    exit;
}

$client_id = (int)$_SESSION['client_id'];
$client_nom = $_SESSION['client_nom'] ?? 'Client';

// Récupération des réservations du client
$query = "
    SELECT r.id_reservation,
           c.id_chambre AS chambre_id,
           r.date_debut,
           r.date_fin,
           r.nombre_personne,
           r.statut
    FROM reservation r
    JOIN chambre c ON r.id_chambre = c.id_chambre
    WHERE r.id_client = $client_id
    ORDER BY r.date_debut DESC
";
$result = executeQuery($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tableau de Bord Client | HôtelSys</title>
<link rel="icon" type="image/png" href="/Gest_hotel/assets/favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root {
    --gold: #c39d67;
    --dark-gray: #444;
    --light-gray: #f5f6fa;
}
body { background: var(--light-gray); font-family: 'Segoe UI', sans-serif; }
.container { margin-top: 50px; max-width: 1000px; }
h1 { color: var(--gold); margin-bottom: 20px; }
.card { background: #fff; border-radius: 14px; border: 1px solid #eee; box-shadow: 0 6px 18px rgba(0,0,0,0.07); padding: 25px; margin-bottom: 30px; }
.table thead { background: #f3e8d9; color: #5a4a32; }
.table tbody tr:hover { background: #faf4ec !important; }
.btn-logout { margin-bottom: 20px; border-radius: 8px; border: 1px solid var(--gold); color: var(--gold); }
.btn-logout:hover { background: var(--gold); color: #fff; }
.btn-gold { background: var(--gold); color: #fff; border-radius: 8px; font-weight: 600; border: none; }
.btn-gold:hover { background: #b2884f; color: #fff; }
.btn-sm-warning { background-color: #f0ad4e; color: #fff; border-radius: 5px; border: none; padding: 2px 8px; text-decoration: none; }
.btn-sm-warning:hover { background-color: #ec971f; color: #fff; }
.btn-sm-danger { background-color: #d9534f; color: #fff; border-radius: 5px; border: none; padding: 2px 8px; text-decoration: none; }
.btn-sm-danger:hover { background-color: #c9302c; color: #fff; }
</style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Bonjour, <?= htmlspecialchars($client_nom) ?></h1>
        <a href="<?= PROJECT_ROOT ?>/client_logout.php" class="btn btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
        </a>
    </div>

    <a href="<?= PROJECT_ROOT ?>/index.php#chambres-disponibles" class="btn btn-gold mb-3"><i class="fa-solid fa-plus"></i> Nouvelle réservation</a>

    <div class="card">
        <h3 class="mb-3"><i class="fa-solid fa-calendar-check"></i> Mes réservations</h3>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Chambre</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Nombre de personnes</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (pg_num_rows($result) > 0) {
                    $i = 1;
                    while ($row = pg_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>".($i++)."</td>";
                        echo "<td>".htmlspecialchars($row['chambre_id'])."</td>";
                        echo "<td>".htmlspecialchars($row['date_debut'])."</td>";
                        echo "<td>".htmlspecialchars($row['date_fin'])."</td>";
                        echo "<td>".htmlspecialchars($row['nombre_personne'])."</td>";
                        echo "<td>".htmlspecialchars($row['statut'])."</td>";
                        echo "<td>
                                <a href='modifier_reservation.php?id=".$row['id_reservation']."' class='btn-sm-warning'>Modifier</a>
                                <a href='annuler_reservation.php?id=".$row['id_reservation']."' class='btn-sm-danger' onclick='return confirm(\"Voulez-vous vraiment annuler cette réservation ?\")'>Annuler</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Aucune réservation pour le moment.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <a href="<?= PROJECT_ROOT ?>/index.php" class="btn btn-gold mt-3"><i class="fa-solid fa-home"></i> Retour à l'accueil</a>
    </div>
</div>

</body>
</html>
