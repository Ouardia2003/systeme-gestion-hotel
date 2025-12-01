<?php
$page_specific_title = "Liste des Clients";
require_once __DIR__ . "/../../includes/db.php";

// Récupération des clients
$query = "SELECT id_client, nom, prenom, tel, mail FROM Client ORDER BY nom ASC";
$result = executeQuery($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Clients | HôtelSys</title>
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

.btn-outline-primary, .btn-outline-danger{
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
            <h2><i class="fa-solid fa-users"></i> Clients (<?= pg_num_rows($result); ?>)</h2>
            <a href="ajouter.php" class="btn btn-gold"><i class="fa-solid fa-plus"></i> Ajouter un Client</a>
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
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ligne = pg_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $ligne['id_client']; ?></td>
                            <td><?= htmlspecialchars($ligne['nom']); ?></td>
                            <td><?= htmlspecialchars($ligne['prenom']); ?></td>
                            <td><?= htmlspecialchars($ligne['tel']); ?></td>
                            <td><?= htmlspecialchars($ligne['mail']); ?></td>
                            <td>
                                <a href="modifier.php?id=<?= $ligne['id_client']; ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i> Modifier</a>
                                <a href="supprimer.php?id=<?= $ligne['id_client']; ?>" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Voulez-vous vraiment supprimer ce client ?');"><i class="fa-solid fa-trash"></i> Supprimer</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info">Aucun client trouvé.</div>
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
