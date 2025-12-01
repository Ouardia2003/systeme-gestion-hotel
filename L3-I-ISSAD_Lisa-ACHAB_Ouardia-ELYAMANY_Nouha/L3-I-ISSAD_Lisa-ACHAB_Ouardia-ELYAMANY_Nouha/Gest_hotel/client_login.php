<?php
session_start();
require_once __DIR__ . '/includes/config.php';

$err = '';
$email_saisi = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_saisi = trim($_POST['client_email'] ?? '');
    $password_saisi = trim($_POST['client_password'] ?? '');

    if (empty($email_saisi) || empty($password_saisi)) {
        $err = "Veuillez remplir tous les champs.";
    } else {
        $clean_email = pg_escape_string($db, $email_saisi);

        $query = "SELECT id_client, nom, prenom, mail, mot_de_passe
                  FROM Client
                  WHERE LOWER(TRIM(mail)) = LOWER(TRIM('$clean_email'))";

        $result = pg_query($db, $query);

        if ($result && pg_num_rows($result) === 1) {
            $client = pg_fetch_assoc($result);

            if (password_verify($password_saisi, $client['mot_de_passe'])) {
                $_SESSION['client_id'] = $client['id_client'];
                $_SESSION['client_nom'] = $client['prenom'] . ' ' . $client['nom'];
                $_SESSION['client_role'] = 'CLIENT';

                // ✅ Redirection vers le dashboard
                header('Location: ' . PROJECT_ROOT . '/client_dashboard.php');
                exit;
            } else {
                $err = "Mot de passe incorrect.";
            }
        } else {
            $err = "Aucun compte client trouvé avec cet email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion Client</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { display:flex; justify-content:center; align-items:center; min-height:100vh; background:#f5f5f5; font-family:sans-serif; }
.login-card { background:#fff; padding:40px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15); width:100%; max-width:450px; text-align:center;}
.login-card h2 { color:#c39d67; margin-bottom:25px; }
input { width:100%; padding:12px 15px; margin-top:10px; border-radius:8px; border:1px solid #ccc; }
input:focus { outline:none; border-color:#c39d67; box-shadow:0 0 8px rgba(195,157,103,0.5); }
.btn-gold { background:#c39d67; color:white; padding:12px 20px; margin-top:30px; border:none; width:100%; border-radius:8px; }
.btn-gold:hover { background:#b18859; }
.alert { margin-bottom:20px; border-radius:8px; padding:10px;}
</style>
</head>
<body>
<div class="login-card">
<h2>Connexion Client</h2>

<?php if($err): ?>
<div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<form method="post">
<input type="email" name="client_email" placeholder="Email" required value="<?= htmlspecialchars($email_saisi) ?>">
<input type="password" name="client_password" placeholder="Mot de passe" required>
<button type="submit" class="btn-gold">Se connecter</button>
</form>

<div class="mt-3">
<a href="<?= PROJECT_ROOT ?>/client_register.php" style="color:#c39d67;">Créer un compte</a> |
<a href="<?= PROJECT_ROOT ?>/index.php" style="color:#c39d67;">Retour à l'accueil</a>
</div>
</div>
</body>
</html>
