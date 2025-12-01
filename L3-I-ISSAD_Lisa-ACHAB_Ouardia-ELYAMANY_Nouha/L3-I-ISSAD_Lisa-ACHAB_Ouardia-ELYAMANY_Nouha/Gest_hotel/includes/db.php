<?php
// Connexion PostgreSQL pour Gest_hotel

$db_host = "postgresql-nouhaelyamany.alwaysdata.net";
$db_port = "5432";
$db_name = "nouhaelyamany_hotel_db";
$db_user = "nouhaelyamany";
$db_password = "8EcsL7GD4zJG!@e";

$conn_string = "host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_password";

$db = pg_connect($conn_string);

if (!$db) {
    die("Erreur de connexion à la base PostgreSQL.");
}

// Fonction pour exécuter une requête
function executeQuery($query) {
    global $db;
    return pg_query($db, $query);
}
?>
