<?php
require_once __DIR__ . '/../../includes/config.php'; 

$message = '';
$clients = executeQuery("SELECT id_client, nom, prenom FROM Client ORDER BY nom ASC");
$chambres = executeQuery("SELECT id_chambre, etage FROM Chambre WHERE statut='libre' ORDER BY id_chambre ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_client = (int)$_POST['id_client'];
    $id_chambre = (int)$_POST['id_chambre'];
    $nombre_personne = (int)($_POST['nombre_personne'] ?? 1); 
    $date_debut = pg_escape_literal($db, $_POST['date_debut']);
    $date_fin = pg_escape_literal($db, $_POST['date_fin']);
    $statut = pg_escape_literal($db, 'confirmée');

    if (strtotime(trim($_POST['date_debut'], "'")) >= strtotime(trim($_POST['date_fin'], "'"))) {
        $message = '<div class="alert alert-warning">La date de début doit être avant la date de fin.</div>';
    } else {
        $query = "INSERT INTO Reservation (id_client, id_chambre, date_debut, date_fin, statut, nombre_personne) 
                  VALUES ($id_client, $id_chambre, $date_debut, $date_fin, $statut, $nombre_personne) 
                  RETURNING id_reservation";
        $res = executeQuery($query);

        if ($res) {
            $new_id = pg_fetch_result($res, 0, 'id_reservation');
            executeQuery("UPDATE Chambre SET statut='occupée' WHERE id_chambre=$id_chambre");
            $message = '<div class="alert alert-success">Réservation n°'.$new_id.' créée avec succès.</div>';

            // Recharger les listes
            $clients = executeQuery("SELECT id_client, nom, prenom FROM Client ORDER BY nom ASC");
            $chambres = executeQuery("SELECT id_chambre, etage FROM Chambre WHERE statut='libre' ORDER BY id_chambre ASC");
        } else {
            $message = '<div class="alert alert-danger">Erreur lors de la création : ' . pg_last_error($db) . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle Réservation | HôtelSys</title>
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
    margin-top:20px;
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

.form-label{
    font-weight:500;
}

.form-control, .form-select{
    border-radius:8px;
    border:1px solid #ccc;
    padding:8px 12px;
    transition:0.2s;
}

.form-control:focus, .form-select:focus{
    border-color:var(--gold);
    box-shadow:0 0 5px rgba(195,157,103,0.4);
    outline:none;
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

.alert{
    border-radius:10px;
    padding:12px 18px;
    font-weight:500;
}

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
            <h2><i class="fa-solid fa-plus"></i> Nouvelle Réservation</h2>
            <a href="liste.php" class="btn btn-gold"><i class="fa-solid fa-arrow-left"></i> Retour à la liste</a>
        </div>
        <div class="card-body">
            <?= $message ?>
            <form method="POST" action="ajouter.php">
                
                <div class="mb-3">
                    <label for="id_client" class="form-label">Client</label>
                    <select name="id_client" id="id_client" class="form-select" required>
                        <option value="" disabled selected>Choisir un client</option>
                        <?php while($c=pg_fetch_assoc($clients)): ?>
                            <option value="<?= $c['id_client'] ?>"><?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_chambre" class="form-label">Chambre Libre</label>
                    <select name="id_chambre" id="id_chambre" class="form-select" required>
                        <option value="" disabled selected>Choisir une chambre</option>
                        <?php while($ch=pg_fetch_assoc($chambres)): ?>
                            <option value="<?= $ch['id_chambre'] ?>">Chambre n°<?= $ch['id_chambre'] ?> (Étage <?= $ch['etage'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nombre_personne" class="form-label">Nombre de personnes</label>
                    <input type="number" name="nombre_personne" id="nombre_personne" class="form-control" min="1" value="1" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_debut" class="form-label">Date Début</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date_fin" class="form-label">Date Fin</label>
                        <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold"><i class="fa-solid fa-check"></i> Créer</button>
            </form>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
