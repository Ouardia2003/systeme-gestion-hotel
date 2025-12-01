<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['employe_id'])) {
    header("Location: ../login.php?error=Connexion+requise");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

// Fonction utilitaire
function getValue($query) {
    $res = executeQuery($query);
    if (!$res) return 0;
    return pg_fetch_result($res, 0, 0);
}

// Statistiques
$nb_chambres_libres      = getValue("SELECT COUNT(*) FROM chambre WHERE statut='libre'");
$nb_chambres_total       = getValue("SELECT COUNT(*) FROM chambre");
$nb_reservations_day     = getValue("SELECT COUNT(*) FROM reservation WHERE date_debut = CURRENT_DATE");
$nb_clients              = getValue("SELECT COUNT(*) FROM client");
$nb_employes             = getValue("SELECT COUNT(*) FROM employe");

// Dernières réservations
$latest_reservations_res = executeQuery("
    SELECT 
        r.id_reservation AS id,
        c.prenom || ' ' || c.nom AS client_nom,
        ch.id_chambre AS chambre_numero,
        r.date_debut,
        r.date_fin
    FROM reservation r
    JOIN client c ON r.id_client = c.id_client
    JOIN chambre ch ON r.id_chambre = ch.id_chambre
    ORDER BY r.date_debut DESC
    LIMIT 5
");

$latest_reservations = [];
if ($latest_reservations_res) {
    while ($row = pg_fetch_assoc($latest_reservations_res)) {
        $latest_reservations[] = $row;
    }
}

$nb_chambres_maintenance = getValue("SELECT COUNT(*) FROM chambre WHERE statut='maintenance'");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard | Hôtelsys</title>
<link rel="icon" type="image/png" href="/Gest_hotel/assets/favicon.png">


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --gold: #c39d67;
    --dark-gray: #444;
    --light-gray: #f5f6fa;
}

body {
    background: var(--light-gray);
    font-family: 'Segoe UI', sans-serif;
}

.card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #eee;
    box-shadow: 0 6px 18px rgba(0,0,0,0.07);
    padding: 25px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

.card-border { border-left: 6px solid var(--gold); }

.stat { 
    font-size: 36px;
    font-weight: bold; 
    color: #555; 
    margin: 10px 0;
}

.btn-gold { 
    border: 1px solid var(--gold); 
    color: var(--gold); 
    border-radius: 8px; 
    align-self: start; 
}

.btn-gold:hover { 
    background: var(--gold); 
    color: #fff; 
}

#chambreChart {
    max-height: 250px; 
    width: 100%;
}

.logout-btn {
    border-radius: 8px;
}
</style>
</head>

<body>

<div class="container mt-5 mb-5">

    <!-- En-tête avec bouton Déconnexion -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 style="color:var(--gold);font-weight:700;">Bienvenue, <?= htmlspecialchars($_SESSION["employe_nom"]); ?></h1>
            <p class="lead">Tableau de bord central</p>
        </div>
        <div>
            <a href="logout.php" class="btn btn-danger logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-4">
        <div class="col-md-3 d-flex">
            <div class="card card-border w-100 h-100">
                <h5><i class="fa-solid fa-bed"></i> Chambres Libres</h5>
                <p class="stat"><?= $nb_chambres_libres ?></p>
                <p>sur <?= $nb_chambres_total ?> chambres</p>
                <a href="chambres/liste.php" class="btn btn-gold">Gérer</a>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card card-border w-100 h-100">
                <h5><i class="fa-solid fa-calendar-check"></i> Réservations Aujourd’hui</h5>
                <p class="stat"><?= $nb_reservations_day ?></p>
                <a href="reservations/liste.php" class="btn btn-gold">Gérer</a>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card card-border w-100 h-100">
                <h5><i class="fa-solid fa-users"></i> Clients</h5>
                <p class="stat"><?= $nb_clients ?></p>
                <a href="clients/liste.php" class="btn btn-gold">Gérer</a>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card card-border w-100 h-100">
                <h5><i class="fa-solid fa-user-tie"></i> Employés</h5>
                <p class="stat"><?= $nb_employes ?></p>
                <a href="employes/liste.php" class="btn btn-gold">Gérer</a>
            </div>
        </div>
    </div>

    <!-- Graphique + Tableau -->
    <div class="row mt-5">
        <div class="col-md-6 d-flex">
            <div class="card w-100 h-100">
                <h5>Occupation des chambres</h5>
                <canvas id="chambreChart"></canvas>
            </div>
        </div>

        <div class="col-md-6 d-flex">
            <div class="card w-100 h-100">
                <h5>Dernières réservations</h5>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Chambre</th>
                            <th>Début</th>
                            <th>Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($latest_reservations as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['client_nom']) ?></td>
                            <td><?= htmlspecialchars($row['chambre_numero']) ?></td>
                            <td><?= $row['date_debut'] ?></td>
                            <td><?= $row['date_fin'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="reservations/liste.php" class="btn btn-gold">Voir toutes</a>
            </div>
        </div>
    </div>

    <?php if($nb_chambres_maintenance > 0): ?>
    <div class="alert alert-warning mt-4">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <?= $nb_chambres_maintenance ?> chambre(s) en maintenance aujourd'hui.
    </div>
    <?php endif; ?>

</div>

<script>
const ctx = document.getElementById('chambreChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Libres', 'Occupées'],
        datasets: [{
            data: [<?= $nb_chambres_libres ?>, <?= $nb_chambres_total - $nb_chambres_libres ?>],
            backgroundColor: ['#c39d67', '#ddd'],
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>

</body>
</html>
