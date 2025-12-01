<?php
// reserver_chambre.php - VERSION CORRIGÉE (Capacité Max)
session_start();
require_once 'includes/config.php';

// 1. SÉCURITÉ : Le client doit être connecté
if (empty($_SESSION['client_id'])) {
    header('Location: index.php?error_client=' . urlencode('Veuillez vous connecter pour réserver.'));
    exit;
}

// 2. Récupération de l'ID de la chambre
$id_chambre = (int)($_GET['id'] ?? 0);
$message = '';

// --- CORRECTION ICI : On ajoute t.capacite_max dans le SELECT ---
$query_info = "SELECT c.*, t.nom as type_nom, t.description, t.capacite_max 
               FROM Chambre c 
               JOIN Type_Chambre t ON c.id_type = t.id_type 
               WHERE c.id_chambre = $id_chambre AND c.statut = 'libre'";
               
$res_info = pg_query($db, $query_info);

if (!$res_info || pg_num_rows($res_info) === 0) {
    die("Cette chambre n'est plus disponible ou n'existe pas. <a href='index.php'>Retour</a>");
}
$chambre = pg_fetch_assoc($res_info);

// 3. TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $personnes = (int)$_POST['personnes'];
    $client_id = $_SESSION['client_id']; 

    if ($date_debut >= $date_fin) {
        $message = '<div class="alert alert-warning">La date de départ doit être après la date d\'arrivée.</div>';
    } else {
        $sql = "INSERT INTO Reservation (id_client, id_chambre, date_debut, date_fin, nombre_personne, statut) 
                VALUES ($1, $2, $3, $4, $5, 'confirmée') RETURNING id_reservation";
        
        $params = array($client_id, $id_chambre, $date_debut, $date_fin, $personnes);
        $result = pg_query_params($db, $sql, $params);

        if ($result) {
            pg_query($db, "UPDATE Chambre SET statut='occupée' WHERE id_chambre = $id_chambre");
            header("Location: index.php?login_client_info=" . urlencode("Réservation confirmée avec succès ! Merci."));
            exit;
        } else {
            $message = '<div class="alert alert-danger">Erreur technique : ' . pg_last_error($db) . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver - Hôtelsys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; color: #333; font-family: 'Roboto', sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-gold { background-color: #c39d67; color: white; font-weight: bold; border: none; }
        .btn-gold:hover { background-color: #b18859; color: white; }
        .text-gold { color: #c39d67; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card p-4">
                <h2 class="text-center text-gold mb-4">Finaliser votre réservation</h2>
                
                <?= $message ?>

                <div class="alert alert-light border">
                    <h5 class="mb-1"><?= htmlspecialchars($chambre['type_nom']) ?></h5>
                    <p class="mb-0 small text-muted"><?= htmlspecialchars($chambre['description']) ?></p>
                    <hr>
                    <div class="d-flex justify-content-between font-weight-bold">
                        <span>Prix par nuit :</span>
                        <span class="text-gold"><?= $chambre['prix_base'] ?> €</span>
                    </div>
                </div>

                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date d'arrivée</label>
                            <input type="date" name="date_debut" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de départ</label>
                            <input type="date" name="date_fin" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nombre de personnes</label>
                        <input type="number" name="personnes" class="form-control" value="1" min="1" max="<?= $chambre['capacite_max'] ?>" required>
                        <small class="text-muted">Maximum : <?= $chambre['capacite_max'] ?> personnes</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gold btn-lg">Confirmer la réservation</button>
                        <a href="index.php" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>