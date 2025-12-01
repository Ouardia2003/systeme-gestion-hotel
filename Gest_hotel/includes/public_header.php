 <?php
require_once __DIR__ . '/config.php';

$page_title = "Hôtelsys Management"; 
if (isset($page_specific_title)) {
    $page_title = $page_specific_title . " | Hôtelsys"; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= $page_title ?></title> 
    
    <link rel="icon" type="image/png" href="<?= PROJECT_ROOT ?>/assets/favicon.png"> 

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --black: #0a0a0a;
            --gold: #c39d67; 
            --gold-dark: #b18859;
            --white: #fdfdfd;
            --background-light: #f5f5f5; 
            --text-dark: #333333;
            --transition-speed: 0.3s;
            --font-primary: 'Playfair Display', serif; 
            --font-secondary: 'Roboto', sans-serif;
        }

        body {
            font-family: var(--font-secondary);
            background-color: var(--background-light);
            color: var(--text-dark);
        }

        .bg-luxury-light {
            background-color: var(--white) !important; 
            border-bottom: 2px solid var(--gold); 
        }
        
        .navbar-brand strong {
            color: var(--text-dark) !important;
            font-family: var(--font-primary);
            font-size: 1.6rem;
            letter-spacing: 1px;
        }

        .hotel-logo {
            height: 45px;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-dark) !important;
            transition: all var(--transition-speed) ease;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: var(--gold) !important;
        }

        .btn-employee-gold {
            background-color: var(--gold);
            color: var(--white) !important;
            font-weight: 700;
            border-radius: 8px;
            margin-left: 15px !important;
            transition: all var(--transition-speed) ease;
        }
        .btn-employee-gold:hover {
            background-color: var(--gold-dark);
        }
        
        .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .alert-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .alert-warning { background-color: #fff3cd; border-color: #ffeeba; color: #856404; }

    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-luxury-light shadow-sm">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center" href="<?= PROJECT_ROOT ?>/index.php">
            <img src="<?= PROJECT_ROOT ?>/assets/logo.png" class="hotel-logo me-2" alt="Hôtelsys Logo">
            <strong>HÔTELSYS MANAGEMENT</strong>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link" href="<?= PROJECT_ROOT ?>/index.php#chambres-disponibles">Chambres Libres</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link btn btn-sm btn-employee-gold" href="<?= PROJECT_ROOT ?>/login.php">Espace Employé</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">