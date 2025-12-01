<?php
// fix_password.php
require_once 'includes/config.php';

// Le mot de passe qu'on veut
$password_clair = "Azerty123!";

// Le serveur calcule le hash LUI-MÊME (plus d'erreur de copier-coller)
$nouveau_hash = password_hash($password_clair, PASSWORD_BCRYPT);

echo "<h1>Réparation du Mot de passe</h1>";
echo "Connexion à la base : <strong>" . pg_dbname($db) . "</strong><br><br>";

// On met à jour TOUT LE MONDE avec ce mot de passe
$query = "UPDATE Employe SET mot_de_passe = '$nouveau_hash', role = 'RECEPTION'";
$result = pg_query($db, $query);

if ($result) {
    echo "<h2 style='color:green'>SUCCÈS ! ✅</h2>";
    echo "Tous les employés ont maintenant le mot de passe : <strong>$password_clair</strong><br>";
    echo "Essaie de te connecter maintenant.";
} else {
    echo "<h2 style='color:red'>ERREUR SQL ❌</h2>";
    echo pg_last_error($db);
}
?>