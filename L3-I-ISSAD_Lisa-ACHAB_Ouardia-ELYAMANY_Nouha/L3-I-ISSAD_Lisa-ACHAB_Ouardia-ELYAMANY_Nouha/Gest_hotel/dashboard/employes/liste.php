<?php
$page_specific_title = "Liste des Employés";
require_once __DIR__ . "/../../includes/db.php";

$result = executeQuery("SELECT id_employe, nom, prenom, poste, actif FROM Employe ORDER BY nom ASC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Employés | HôtelSys</title>
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

.table thead{
    background:#f3e8d9;
    color:#5a4a32;
}

.table tbody tr:hover{
    background:#faf4ec !important;
}

.btn-outline-primary{
    border-radius:6px;
    font-size:0.85rem;
}

.alert{
    margin-top:15px;
}
</style>
</head>
<body>
<div class="container mt-4">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2><i class="fa-solid fa-user-tie"></i> Employés (<?= pg_num_rows($result) ?>)</h2>
        </div>
        <div class="card-body">
            <?php if ($result && pg_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Poste</th>
                            <th>Actif</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($e = pg_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $e['id_employe'] ?></td>
                            <td><?= htmlspecialchars($e['nom']) ?></td>
                            <td><?= htmlspecialchars($e['prenom']) ?></td>
                            <td><?= htmlspecialchars($e['poste']) ?></td>
                            <td><?= $e['actif'] === 't' ? 'Oui' : 'Non' ?></td>
                            <td>
                                <a href="modifier.php?id=<?= $e['id_employe'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pen"></i> Modifier
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info"><i class="fa-solid fa-info-circle"></i> Aucun employé trouvé.</div>
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
