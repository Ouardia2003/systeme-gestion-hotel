<?php
// includes/config.php

// ----------------------------------------------------
// 1. PARAMÈTRES DE CONNEXION À LA BASE DE DONNÉES
// ----------------------------------------------------
define('DB_HOST', 'postgresql-nouhaelyamany.alwaysdata.net');
define('DB_NAME', 'nouhaelyamany_hotel_db');
define('DB_USER', 'nouhaelyamany');
define('DB_PASSWORD', '8EcsL7GD4zJG!@e'); 
define('DB_PORT', '5432');

// ----------------------------------------------------
// 2. CHEMIN RACINE DU PROJET (PROJECT_ROOT)
// ----------------------------------------------------
define('PROJECT_ROOT', '/Gest_hotel'); 

// ----------------------------------------------------
// 3. CONNEXION À LA BASE DE DONNÉES
// ----------------------------------------------------
$conn_string = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD;

$db = @pg_connect($conn_string);

if (!$db) {
    die("<h1>Erreur fatale de connexion à la base de données.</h1>
        <p>Veuillez vérifier les paramètres (Hôte, Nom BD, Utilisateur, Mot de passe) dans <code>includes/config.php</code>.</p>");
}

// ----------------------------------------------------
// 4. FONCTION D'EXÉCUTION DE REQUÊTE
// ----------------------------------------------------
function executeQuery($query) {
    global $db; 
    
    if (!$db) {
        die("Erreur interne: Tentative d'exécution de requête sans connexion active.");
    }
    
    $result = pg_query($db, $query);
    
    if (!$result) {
        die("Erreur d'exécution de requête : " . pg_last_error($db) . 
            "<br>Requête échouée : <code>" . htmlspecialchars($query) . "</code>");
    }
    
    return $result;
}
?>
