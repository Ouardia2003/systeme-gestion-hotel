<?php
$page_specific_title = "Modifier Client";
require_once __DIR__ . "/../../includes/db.php";

$message = '';
$client_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_client'])) {
    $id_client = (int)$_POST['id_client'];
    $nom = pg_escape_literal($db, trim($_POST['nom']));
    $prenom = pg_escape_literal($db, trim($_POST['prenom']));
    $mail = pg_escape_literal($db, trim($_POST['mail']));
    $tel = pg_escape_literal($db, trim($_POST['tel']));
    $date_naissance = pg_escape_literal($db, $_POST['date_naissance']);
    $adresse = pg_escape_literal($db, trim($_POST['adresse']));

    $query = "UPDATE Client SET nom=$nom, prenom=$prenom, mail=$mail, tel=$tel,
              date_naissance=$date_naissance, adresse=$adresse WHERE id_client=$id_client";

    if (executeQuery($query)) {
        $message = '<div class="alert alert-success">Client mis à jour avec succès !</div>';
    } else {
        $message = '<div class="alert alert-danger">Erreur lors de la mise à jour.</div>';
    }
}

$query_select = "SELECT * FROM Client WHERE id_client=$client_id";
$result = executeQuery($query_select);

if (pg_num_rows($result) === 0) {
    echo '<div class="alert alert-warning">Client non trouvé.</div>';
} else {
    $client = pg_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Client | HôtelSys</title>
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

.card-header h3{
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

.form-label{
    font-weight:600;
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
            <h3><i class="fa-solid fa-user-pen"></i> Modifier le client n°<?= $client['id_client'] ?></h3>
            <a href="liste.php" class="btn btn-gold"><i class="fa-solid fa-arrow-left"></i> Retour</a>
        </div>
        <div class="card-body">
            <?= $message ?>
            <form method="POST" action="modifier.php?id=<?= $client['id_client'] ?>">
                <input type="hidden" name="id_client" value="<?= $client['id_client'] ?>">
                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($client['nom']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($client['prenom']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="mail" class="form-control" value="<?= htmlspecialchars($client['mail']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="tel" class="form-control" value="<?= htmlspecialchars($client['tel']) ?>" maxlength="10" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($client['date_naissance']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($client['adresse']) ?>" required>
                </div>
                <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                <a href="liste.php" class="btn btn-secondary"><i class="fa-solid fa-times"></i> Annuler</a>
            </form>
        </div>
    </div>

</div>
</body>
</html>

<?php
}
pg_free_result($result);
require_once __DIR__ . '/../includes/admin_footer.php';
?>
