<?php
// login.php (Espace Employé) - VERSION CORRIGÉE FORCEE
session_start();
require_once __DIR__ . '/includes/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username'] ?? ''); 
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
    } else {
        
        // ICI : On demande EXPLICITEMENT le mot_de_passe
        // Si la colonne n'existe pas, SQL nous renverra une erreur claire au lieu de juste l'ignorer.
        $query = "SELECT id_employe, nom, prenom, mail_pro, mot_de_passe, role, actif 
                  FROM employe 
                  WHERE TRIM(mail_pro) = '" . pg_escape_string($db, $email) . "'";
        
        $result = pg_query($db, $query);

        if ($result && pg_num_rows($result) === 1) {
            $employe = pg_fetch_assoc($result);

            // Maintenant, la clé 'mot_de_passe' est obligée d'exister
            if (password_verify($password, $employe['mot_de_passe'])) {
                 
                 // Attention : postgres stocke les booléens sous forme de 't' (true) ou 'f' (false)
                 if ($employe['actif'] === 'f') { 
                     $message = '<div class="alert alert-danger">Compte désactivé.</div>';
                } else {
                    $_SESSION['employe_id'] = $employe['id_employe'];
                    $_SESSION['employe_nom'] = $employe['prenom'] . ' ' . $employe['nom'];
                    $_SESSION['employe_role'] = $employe['role'];
                    
                    header('Location: ' . PROJECT_ROOT . '/dashboard/dashboard.php');
                    exit;
                }

            } else {
                $message = '<div class="alert alert-danger">Mot de passe incorrect.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Email inconnu.</div>';
        }
    } 
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Employé - Hôtelsys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --background-light: #f5f5f5; --card-light: #ffffff; --text-dark: #333333; --gold: #c39d67; --gold-dark: #b18859; --border-radius: 12px; --font-primary: 'Playfair Display', serif; --font-secondary: 'Roboto', sans-serif; }
        body { font-family: var(--font-secondary); background-color: var(--background-light); color: var(--text-dark); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-card { background-color: var(--card-light); padding: 40px; border-radius: var(--border-radius); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); max-width: 450px; width: 100%; text-align: center; }
        .login-card h2 { font-family: var(--font-primary); color: var(--gold); font-size: 2.5rem; margin-bottom: 25px; }
        .login-card label { display: block; text-align: left; margin-bottom: 5px; font-weight: 500; color: var(--text-dark); margin-top: 15px; }
        .login-card input[type="text"], .login-card input[type="password"] { width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #cccccc; background-color: var(--card-light); color: var(--text-dark); font-size: 1rem; box-sizing: border-box; }
        .login-card input:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 8px rgba(195, 157, 103, 0.5); }
        .btn-gold { background-color: var(--gold); color: var(--card-light); font-weight: 700; border-radius: 8px; padding: 12px 20px; text-transform: uppercase; letter-spacing: 1px; border: none; width: 100%; margin-top: 30px; transition: background-color 0.3s; }
        .btn-gold:hover { background-color: var(--gold-dark); }
        .alert { border-radius: 8px; padding: 10px; margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Espace Employé</h2>

    <?= $message ?>

    <form method="post">
        <label for="username">Email Pro</label>
        <input type="text" id="username" name="username" placeholder="Entrez votre email" required value="<?= htmlspecialchars($email ?? '') ?>">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>

        <button type="submit" class="btn-gold">Se connecter</button>
    </form>
    
    <div class="mt-4">
        <a href="<?= PROJECT_ROOT ?>/index.php" style="color: var(--gold); text-decoration: none; font-weight: 500;">&larr; Retour à l'accueil</a>
    </div>
</div>

</body>
</html>