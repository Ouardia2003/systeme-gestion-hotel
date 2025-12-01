<?php
$page_specific_title = "Modifier Employé";
require_once __DIR__ . "/../../includes/db.php";

$message = '';
$postes_possibles = ['Réceptionniste','Femme de ménage','Manager','Technicien'];
$employe_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id_employe'])) {
    $id_employe = (int)$_POST['id_employe'];
    $nom = pg_escape_literal($db, $_POST['nom']);
    $prenom = pg_escape_literal($db, $_POST['prenom']);
    $poste = pg_escape_literal($db, $_POST['poste']);
    $salaire = (float)$_POST['salaire'];
    $actif = isset($_POST['actif']) ? 'TRUE' : 'FALSE';
    $mail_pro = pg_escape_literal($db, $_POST['mail_pro']);
    executeQuery("UPDATE Employe SET nom=$nom, prenom=$prenom, poste=$poste, salaire=$salaire, actif=$actif, mail_pro=$mail_pro WHERE id_employe=$id_employe");
    $message = '<div class="alert alert-success"><i class="fa-solid fa-check"></i> Employé mis à jour</div>';
}

$employe = null;
if ($employe_id) {
    $res = executeQuery("SELECT * FROM Employe WHERE id_employe=$employe_id");
    if(pg_num_rows($res)>0) $employe=pg_fetch_assoc($res);
    pg_free_result($res);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Employé | HôtelSys</title>
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
            <h3><i class="fa-solid fa-user-pen"></i> Modifier Employé n°<?= $employe['id_employe'] ?? '' ?></h3>
            <a href="liste.php" class="btn btn-gold"><i class="fa-solid fa-arrow-left"></i> Retour</a>
        </div>
        <div class="card-body">
            <?= $message ?>
            <?php if($employe): ?>
            <form method="POST" action="modifier.php?id=<?= $employe['id_employe'] ?>">
                <input type="hidden" name="id_employe" value="<?= $employe['id_employe'] ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($employe['nom']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($employe['prenom']) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Poste</label>
                    <select name="poste" class="form-select">
                        <?php foreach($postes_possibles as $p): ?>
                        <option value="<?= $p ?>" <?= $p==$employe['poste']?'selected':'' ?>><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Salaire (€)</label>
                    <input type="number" step="0.01" name="salaire" class="form-control" value="<?= htmlspecialchars($employe['salaire']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Professionnel</label>
                    <input type="email" name="mail_pro" class="form-control" value="<?= htmlspecialchars($employe['mail_pro']) ?>" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="actif" <?= $employe['actif']=='t'?'checked':'' ?>>
                    <label class="form-check-label">Employé Actif</label>
                </div>
                <button type="submit" class="btn btn-gold"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                <a href="liste.php" class="btn btn-secondary"><i class="fa-solid fa-times"></i> Annuler</a>
            </form>
            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
