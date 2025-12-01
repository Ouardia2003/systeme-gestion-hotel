<?php
// ----------------------------------------------------
// 1. Définition des paramètres de connexion
// ----------------------------------------------------
$host = "postgresql-nouhaelyamany.alwaysdata.net";
$dbname = "nouhaelyamany_hotel_db";
$user = "nouhaelyamany"; 
$password = "8EcsL7GD4zJG!@e";
$port = "5432";


$conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";

// ----------------------------------------------------
// 2. Connexion au serveur et gestion des erreurs
// ----------------------------------------------------
$connexion = @pg_connect($conn_string); // Le @ masque les erreurs par défaut, on les gère après

if (!$connexion) {
    
    die("<h1>ÉCHEC DE CONNEXION !</h1><p>Vérifiez vos paramètres (Hôte, MDP) dans le fichier PHP.</p> Erreur détaillée : " . pg_last_error());
}

echo "<h1> Connexion PostgreSQL réussie !</h1>";

// ----------------------------------------------------
// 3. Exemple d'exécution d'une requête SELECT
// ----------------------------------------------------
$resultat = pg_query($connexion, "SELECT nom, prenom FROM Client ORDER BY id_client LIMIT 5;");

if (!$resultat) {
    die("<h2>Erreur dans la requête SQL !</h2>" . pg_last_error());
}

echo "<h2>Liste des 5 premiers clients (test de lecture) :</h2>";
echo "<ul>";

// Parcours des résultats
while ($ligne = pg_fetch_assoc($resultat)) {
    echo "<li>" . $ligne['prenom'] . " " . $ligne['nom'] . "</li>";
}
echo "</ul>";

// ----------------------------------------------------
// 4. Libération et 5. Fermeture de la connexion
// ----------------------------------------------------
pg_free_result($resultat);
pg_close($connexion);

?>