<?php
$page_specific_title = "Liste des Chambres";

require_once __DIR__ . "/../../includes/db.php";

// Requête
$query = "SELECT id_chambre, etage, statut FROM Chambre ORDER BY id_chambre ASC";
$result = executeQuery($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Chambres | HôtelSys</title>
<link rel="icon" type="image/png" href="/Gest_hotel/assets/favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
:root{
    --gold:#c39d67;
}

body{
    background:#f5f6fa;
    font-family:'Segoe UI',sans-serif;
}

/* En-tête */
.page-title{
    color:var(--gold);
    font-weight:700;
    margin-top:20px;
    text-align:center;
}

/* Carte */
.card{
    border:1px solid #eee;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.07);
    margin-top:30px;
}

/* Header de carte */
.card-header{
    background:white;
    border-bottom:1px solid #eee;
    padding:18px;
}

.card-header h2{
    margin:0;
    font-size:1.4rem;
    font-weight:700;
    color:var(--gold);
}

/* Table */
.table thead{
    background:#faf7f2;
    border-bottom:2px solid var(--gold);
}

.table tbody tr:hover{
    background:#f9f3ea;
}

/* Bouton or */
.btn-gold{
    border:1px solid var(--gold);
    color:var(--gold);
    padding:6px 14px;
    border-radius:8px;
    font-weight:500;
    transition:0.2s;
}

.btn-gold:hover{
    background:var(--gold);
    color:white;
}
</style>
</head>

<body>

<div class="container">

    <h1 class="page-title"><i class="fa-solid fa-bed"></i> Liste des Chambres</h1>

    <div class="card">

        <div class="card-header d-flex justify-content-between">
            <h2>Chambres (<?= pg_num_rows($result); ?>)</h2>
        </div>

        <div class="card-body">

            <?php if ($result && pg_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Étage</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php while ($ligne = pg_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $ligne['id_chambre']; ?></td>
                            <td><?= htmlspecialchars($ligne['etage']); ?></td>
                            <td><?= htmlspecialchars($ligne['statut']); ?></td>
                            <td>
                                <a href="modifier_statut.php?id=<?= $ligne['id_chambre']; ?>" class="btn btn-gold btn-sm">
                                    Modifier Statut
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

            <?php else: ?>
                <div class="alert alert-warning">Aucune chambre trouvée.</div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>

<?php
pg_free_result($result);
?>
