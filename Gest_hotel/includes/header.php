 <?php


if (!isset($_SESSION['user_id'])) {
  
    require_once __DIR__ . '/config.php';
    
    header('Location: ' . PROJECT_ROOT . '/login.php');
    exit;
}

if (!defined('PROJECT_ROOT')) { 
    require_once __DIR__ . '/config.php';
}


if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) { 
    session_unset();
    session_destroy();
    header('Location: ' . PROJECT_ROOT . '/logout.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time(); 


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Hôtelière</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= PROJECT_ROOT ?>/index.php"> Gest-Hotel</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/chambres/liste.php">Chambres</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/reservations/liste.php">Réservations</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/clients/liste.php">Clients</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= PROJECT_ROOT ?>/employes/liste.php">Employés</a></li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white-50">
                        Connecté(e) : <?= htmlspecialchars($_SESSION['user_role'] ?? 'Employé') ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger btn-sm" href="<?= PROJECT_ROOT ?>/logout.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">