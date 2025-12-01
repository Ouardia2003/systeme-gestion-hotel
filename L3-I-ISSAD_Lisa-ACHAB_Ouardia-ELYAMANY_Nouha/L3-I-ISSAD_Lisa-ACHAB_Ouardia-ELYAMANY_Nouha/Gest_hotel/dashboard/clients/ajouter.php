<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$page_specific_title = "Ajouter Client";
require_once __DIR__ . "/../../includes/db.php";

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = pg_escape_literal($db, trim($_POST['nom']));
    $prenom = pg_escape_literal($db, trim($_POST['prenom']));
    $mail = pg_escape_literal($db, trim($_POST['mail']));
    $tel = pg_escape_literal($db, trim($_POST['tel']));
    $date_naissance = pg_escape_literal($db, $_POST['date_naissance']);
    $adresse = pg_escape_literal($db, trim($_POST['adresse']));

    $query = "INSERT INTO Client (nom, prenom, mail, tel, date_naissance, adresse) 
              VALUES ($nom, $prenom, $mail, $tel, $date_naissance, $adresse)";
    $result = executeQuery($query);

    $message = $result
        ? '<div class="alert alert-success">Client ajouté avec succès !</div>'
        : '<div class="alert alert-danger">Erreur lors de l\'ajout du client.</div>';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter Client | HôtelSys</title>
<link rel="icon" type="image/png" href="/Gest_hotel/assets/favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--gold:#c39d67;}
body{background:#f5f6fa;font-family:"Segoe UI",sans-serif;}
.card{border:1px solid #e7e7e7;border-radius:14px;box-shadow:0 6px 18px rgba(0,0,0,0.07);}
.card-header{background:white !important;border-bottom:1px solid var(--gold);padding:18px;}
.card-header h3{color:var(--gold);font-weight:700;margin:0;}
.btn-gold{background:var(--gold);color:white;border-radius:8px;font-weight:600;}
.btn-gold:hover{background:#a8834f;color:white;}
.form-label{font-weight:600;}
.alert{margin-top:15px;}
</style>
</head>
<body>
<div class="container mt-4">

    <div class="card">
        <div class="card-header">
            <h3>Ajouter un client</h3>
        </div>
        <div class="card-body">
            <?= $message ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="mail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="tel" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_naissance" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-gold">Ajouter</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>
