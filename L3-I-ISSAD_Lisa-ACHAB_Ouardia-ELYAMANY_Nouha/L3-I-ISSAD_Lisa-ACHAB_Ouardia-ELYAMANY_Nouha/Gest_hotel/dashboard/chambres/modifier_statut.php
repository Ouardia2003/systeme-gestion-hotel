<?php
require_once __DIR__ . "/../../includes/db.php";

$message = '';
$statuts_possibles = ['libre', 'occupée', 'maintenance', 'nettoyage'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_chambre'])) {
    $id_chambre = (int)$_POST['id_chambre'];
    $nouveau_statut = trim($_POST['statut']);

    if (in_array($nouveau_statut, $statuts_possibles)) {
        $val = pg_escape_literal($db, $nouveau_statut);
        $query = "UPDATE Chambre SET statut = $val WHERE id_chambre = $id_chambre";

        if (executeQuery($query)) {
            $message = '<div class="alert alert-success">Statut mis à jour avec succès.</div>';
        } else {
            $message = '<div class="alert alert-danger">Erreur lors de la mise à jour.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Statut invalide.</div>';
    }
}

$chambre_id = $_GET['id'] ?? ($_POST['id_chambre'] ?? null);
$chambre = null;

if ($chambre_id) {
    $res = executeQuery("SELECT id_chambre, etage, statut FROM Chambre WHERE id_chambre = " . (int)$chambre_id);
    if ($res && pg_num_rows($res) > 0) {
        $chambre = pg_fetch_assoc($res);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Statut | HôtelSys</title>
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

/* Card luxe */
.card{
    border:1px solid #eee;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.07);
    margin-top:40px;
}

/* Header doré */
.card-header{
    background:white;
    border-bottom:1px solid var(--gold);
    padding:18px;
}

.card-header h3{
    margin:0;
    color:var(--gold);
    font-weight:700;
}

/* Boutons gold */
.btn-gold{
    border:1px solid var(--gold);
    color:var(--gold);
    border-radius:8px;
    font-weight:500;
    padding:7px 18px;
}

.btn-gold:hover{
    background:var(--gold);
    color:white;
}

.btn-grey{
    border:1px solid #aaa;
    color:#666;
    border-radius:8px;
}

.btn-grey:hover{
    background:#ddd;
}
</style>
</head>

<body>
<div class="container">

    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fa-solid fa-bed"></i>
                Modifier le Statut — Chambre n°<?= $chambre['id_chambre'] ?? '' ?>
            </h3>
        </div>

        <div class="card-body">

            <?= $message ?>

            <?php if ($chambre): ?>
            <form method="POST">

                <input type="hidden" name="id_chambre" value="<?= $chambre['id_chambre']; ?>">

                <div class="mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select" required>
                        <?php foreach ($statuts_possibles as $s): ?>
                            <option value="<?= $s ?>" <?= ($s == $chambre['statut']) ? 'selected' : '' ?>>
                                <?= ucfirst($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn btn-gold">Mettre à Jour</button>
                <a href="liste.php" class="btn btn-grey">Retour</a>
            </form>
            <?php else: ?>
                <div class="alert alert-warning">Chambre introuvable.</div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>
