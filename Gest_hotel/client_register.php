<?php
// client_register.php
session_start();
require_once 'includes/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Récupération et nettoyage des données
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $tel = str_replace([' ', '.', '-', '/'], '', trim($_POST['tel']));
    $date_naissance = $_POST['date_naissance'];
    $adresse = trim($_POST['adresse']);
    $password = $_POST['password'];

    // 2. Vérifications basiques
    if (empty($nom) || empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">Veuillez remplir les champs obligatoires.</div>';
    } else {
        // 3. Hachage du mot de passe
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 4. Insertion SQL
        // On utilise des paramètres ($1, $2...) pour la sécurité
        $query = "INSERT INTO Client (nom, prenom, mail, tel, date_naissance, adresse, mot_de_passe) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7)";
        
        $params = array($nom, $prenom, $email, $tel, $date_naissance, $adresse, $password_hash);
        
        // Exécution sécurisée
        $result = pg_query_params($db, $query, $params);

        if ($result) {
            // Succès : On redirige vers la connexion avec un message
            header('Location: index.php?register=success');
            exit;
        } else {
            // Erreur (souvent email ou tel déjà pris)
            $error = pg_last_error($db);
            if (strpos($error, 'unique') !== false) {
                $message = '<div class="alert alert-danger">Cet email ou ce numéro de téléphone existe déjà.</div>';
            } else {
                $message = '<div class="alert alert-danger">Erreur technique lors de l\'inscription.</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Hôtel</title>
    <link rel="icon" type="image/png" href="/Gest_hotel/assets/favicon.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="card shadow p-4" style="max-width: 500px; width: 100%;">
    <h2 class="text-center mb-4" style="color: #c39d67;">Créer un compte</h2>
    
    <?= $message ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nom *</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Prénom *</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="tel" name="tel" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Adresse</label>
            <textarea name="adresse" class="form-control" rows="2" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe *</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100" style="background-color: #c39d67; border: none;">S'inscrire</button>
    </form>
    
    <div class="text-center mt-3">
        <a href="index.php" class="text-secondary">Déjà un compte ? Se connecter</a>
    </div>
</div>

</body>
</html>