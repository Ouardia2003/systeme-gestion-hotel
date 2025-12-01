<?php
// Définition du titre de la page
$page_specific_title = "Tableau de Bord";

// Importation du header (menu + sécurité)
// CORRIGÉ : On remonte d'un niveau (..) pour trouver /includes/admin_header.php
require_once __DIR__ . '/../includes/admin_header.php';

// --- Données temporaires (à remplacer par vos requêtes SQL) ---
$nb_chambres_libres = 4; // Exemple, à remplacer par une requête BDD
$nb_reservations_aujourdhui = 2; // Exemple, à remplacer par une requête BDD
$nb_employes = 5; // Exemple, à remplacer par une requête BDD
?>

<h1 class="mb-4">Bienvenue, <?= htmlspecialchars($_SESSION['employe_nom'] ?? 'Admin') ?></h1>
<p class="lead">Ceci est votre tableau de bord central. Utilisez le menu ci-dessus pour accéder aux fonctionnalités.</p>

<div class="row mt-5">

    <!-- Chambres -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-left: 6px solid var(--gold);">
            <div class="card-body">
                <h5 class="card-title text-gold">Chambres Libres</h5>
                <h1 class="display-4"><?= $nb_chambres_libres ?></h1>
                <p class="card-text">sur 8 disponibles.</p>

                <!-- CORRIGÉ : Lien vers le sous-dossier dashboard/chambres/liste.php -->
                <a href="<?= PROJECT_ROOT ?>/dashboard/chambres/liste.php" 
                   class="btn btn-sm btn-outline-primary">
                   Gérer les chambres
                </a>
            </div>
        </div>
    </div>

    <!-- Réservations -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-left: 6px solid var(--admin-primary);">
            <div class="card-body">
                <h5 class="card-title" style="color: var(--admin-primary);">Réservations du Jour</h5>
                <h1 class="display-4"><?= $nb_reservations_aujourdhui ?></h1>
                <p class="card-text">Arrivées et départs prévus.</p>

                <!-- CORRIGÉ : Lien vers le sous-dossier dashboard/reservations/liste.php -->
                <a href="<?= PROJECT_ROOT ?>/dashboard/reservations/liste.php" 
                   class="btn btn-sm btn-outline-primary">
                   Gérer les réservations
                </a>
            </div>
        </div>
    </div>

    <!-- Employés -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-left: 6px solid #28a745;">
            <div class="card-body">
                <h5 class="card-title" style="color: #28a745;">Employés Actifs</h5>
                <h1 class="display-4"><?= $nb_employes ?></h1>
                <p class="card-text">Collaborateurs disponibles.</p>

                <!-- CORRIGÉ : Lien vers le sous-dossier dashboard/employes/liste.php -->
                <a href="<?= PROJECT_ROOT ?>/dashboard/employes/liste.php" 
                   class="btn btn-sm btn-outline-primary">
                   Gérer les employés
                </a>
            </div>
        </div>
    </div>

</div>

<?php
// Pied de page
// CORRIGÉ : On remonte d'un niveau (..) pour trouver /includes/admin_footer.php
require_once __DIR__ . '/../includes/admin_footer.php';
?>