 <?php
// Activer affichage des erreurs pendant développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir la racine du projet AlwaysData
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', '/Gest_hotel');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l’employé n'est pas connecté → redirection login
if (!isset($_SESSION['employe_id']) || empty($_SESSION['employe_id'])) {
    header("Location: " . PROJECT_ROOT . "/login.php?error=Veuillez+vous+connecter");
    exit();
}

// Titre page admin
$admin_page_title = isset($page_specific_title)
    ? htmlspecialchars($page_specific_title . " | Admin")
    : "Administration | Hôtelsys";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $admin_page_title ?></title>

    <link rel="icon" type="image/png" href="<?= PROJECT_ROOT ?>/assets/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { --gold: #c39d67; --admin-primary: #007bff; --admin-bg: #e9ecef; --text-dark: #333; }
        body { background-color: var(--admin-bg); color: var(--text-dark); font-family: "Roboto", sans-serif; }
        .admin-navbar { background-color: var(--admin-primary); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .admin-navbar .nav-link, .admin-navbar .navbar-brand { color: #fff !important; font-weight: 500; }
        .admin-navbar .nav-link:hover { color: var(--gold) !important; }
        .btn-deconnexion { background-color: #dc3545; color: #fff; }
        .btn-deconnexion:hover { background-color: #b52a35; }
        main.container-admin { margin-top: 30px; }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg admin-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= PROJECT_ROOT ?>/dashboard/dashboard.php">GESTION HÔTELSYS</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/dashboard/dashboard.php"><i class="fas fa-home"></i> Tableau de Bord</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/dashboard/chambres/liste.php"><i class="fas fa-bed"></i> Chambres</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/dashboard/reservations/liste.php"><i class="fas fa-book"></i> Réservations</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/dashboard/clients/liste.php"><i class="fas fa-users"></i> Clients</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/dashboard/employes/liste.php"><i class="fas fa-user-tie"></i> Employés</a></li>
            </ul>

            <div class="d-flex align-items-center">
                <span class="navbar-text me-3 text-white">
                    Connecté(e) : <strong><?= htmlspecialchars($_SESSION['employe_nom'] ?? "Employé") ?></strong>
                </span>
                <a href="<?= PROJECT_ROOT ?>/logout.php" class="btn btn-sm btn-deconnexion">Déconnexion <i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </div>
</nav>

<main class="container container-admin">
