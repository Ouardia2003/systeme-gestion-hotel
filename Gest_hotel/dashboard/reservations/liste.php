<?php
$page_specific_title = "Liste des Réservations";
require_once __DIR__ . "/../../includes/db.php";

$query = "SELECT r.id_reservation, c.nom, c.prenom, ch.id_chambre, r.date_debut, r.date_fin, r.statut
          FROM Reservation r
          JOIN Client c ON r.id_client = c.id_client
          JOIN Chambre ch ON r.id_chambre = ch.id_chambre
          ORDER BY r.date_debut DESC";
$result = executeQuery($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Réservations | HôtelSys</title>
<link rel="icon" type="image/png" href="/Gest_hotel/assets/favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root{
    --gold:#c39d67;
}

body{
    background:#f5f6fa;
    font-family:"Segoe UI",sans-serif;
}

.card{
    border:1px solid #e7e7e7;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.07);
}

/* Header doré */
.card-header{
    background:white !important;
    border-bottom:1px solid var(--gold);
    padding:18px;
}

.card-header h2{
    color:var(--gold);
    font-weight:700;
    margin:0;
}

/* Bouton or */
.btn-gold{
    background:var(--gold);
    color:white;
    border-radius:8px;
    font-weight:600;
}

.btn-gold:hover{
    background:#a8834f;
    color:white;
}

/* Table stylée */
.table thead{
    background:#f3e8d9;
    color:#5a4a32;
}

.table tbody tr:hover{
    background:#faf4ec !important;
}
</style>
</head>

<body>
<div class="container mt-4">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h2><i class="fa-solid fa-calendar-check"></i> Réservations (<?= pg_num_rows($result); ?>)</h2>
            <a href="ajouter.php" class="btn btn-gold">
                <i class="fa-solid fa-plus"></i> Nouvelle Réservation
            </a>
        </div>

        <div class="card-body">

        <?php if ($result && pg_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Chambre</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($r = pg_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $r['id_reservation']; ?></td>
                            <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']); ?></td>
                            <td><?= $r['id_chambre']; ?></td>
                            <td><?= $r['date_debut']; ?></td>
                            <td><?= $r['date_fin']; ?></td>
                            <td><?= htmlspecialchars($r['statut']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Aucune réservation trouvée.</div>
        <?php endif; ?>

        </div>
    </div>

</div>
</body>
</html>

<?php
pg_free_result($result);
require_once __DIR__ . '/../includes/admin_footer.php';
?>
