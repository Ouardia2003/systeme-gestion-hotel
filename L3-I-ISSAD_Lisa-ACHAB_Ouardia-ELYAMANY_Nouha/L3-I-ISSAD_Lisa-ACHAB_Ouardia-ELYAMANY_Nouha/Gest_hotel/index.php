<?php

$page_specific_title = "Accueil";
require_once __DIR__ . '/includes/public_header.php';

$client_login_message = '';
if (isset($_GET['login_client']) && $_GET['login_client'] === 'success') {
    $client_login_message = '<div class="alert alert-success">Bienvenue ! Connexion client réussie.</div>';
} elseif (isset($_GET['error_client'])) {
    $client_login_message = '<div class="alert alert-danger">Erreur : ' . htmlspecialchars($_GET['error_client']) . '</div>';
} elseif (isset($_GET['logout_client']) && $_GET['logout_client'] === 'success') {
    $client_login_message = '<div class="alert alert-info">Vous êtes déconnecté.</div>';
} elseif (isset($_GET['login_client_info'])) {
    $client_login_message = '<div class="alert alert-info">' . htmlspecialchars($_GET['login_client_info']) . '</div>';
}

$query_chambres = "
    SELECT 
        c.id_chambre,
        tc.nom AS type_nom,
        tc.description,
        c.prix_base,
        tc.capacite_max,
        c.etage
    FROM Chambre c
    JOIN Type_Chambre tc ON c.id_type = tc.id_type
    WHERE c.statut = 'libre'
    ORDER BY c.prix_base ASC
";
$result_chambres = executeQuery($query_chambres);
?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<style>
:root {
    --background-light: #f5f5f5;
    --card-light: #ffffff;
    --text-dark: #333333;
    --gold: #c39d67;
    --gold-dark: #b18859;
    --border-radius: 12px;
    --transition-speed: 0.3s;
    --font-primary: 'Playfair Display', serif;
    --font-secondary: 'Roboto', sans-serif;
}

body {
    background-color: var(--background-light);
    color: var(--text-dark);
    margin: 0;
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-primary);
    color: var(--text-dark);
}
.text-gold {
    color: var(--gold) !important;
}
.my-5 {
    margin-top: 4rem !important;
    margin-bottom: 4rem !important;
}

.hero-section {
    position: relative;
    background-image: url('<?= PROJECT_ROOT ?>/assets/best.png');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding: 150px 20px;
    text-align: center;
    color: var(--text-dark);
    border-bottom: 5px solid var(--gold);
}
.hero-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.6);
    backdrop-filter: blur(4px);
    z-index: 1;
}
.hero-section > .container {
    position: relative;
    z-index: 2;
}

.hero-section h1 {
    font-size: 4.5rem;
    color: var(--gold);
    text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
}
.hero-section p {
    font-size: 1.6rem;
    font-weight: 400;
    color: var(--text-dark);
}

.btn-gold {
    background-color: var(--gold);
    color: var(--card-light);
    font-weight: 700;
    border-radius: var(--border-radius);
    padding: 1rem 2.5rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border: 2px solid var(--gold);
    transition: all var(--transition-speed) ease;
}
.btn-gold:hover {
    background-color: var(--gold-dark);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.btn-outline-light {
    border: 1px solid var(--gold);
    color: var(--text-dark);
    background-color: var(--card-light);
}
.btn-outline-light:hover {
    background-color: var(--gold);
    color: var(--card-light);
}

.card {
    border-radius: var(--border-radius);
    background-color: var(--card-light);
    color: var(--text-dark);
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border: none;
    transition: all var(--transition-speed) ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

input.form-control {
    border-radius: 8px;
    border: 1px solid #cccccc;
    background-color: var(--card-light);
    color: var(--text-dark);
    padding: 0.8rem 1.2rem;
}
input.form-control:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 8px rgba(195, 157, 103, 0.5);
}

.room-price {
    color: var(--gold);
    font-weight: 700;
    font-size: 1.6rem;
}
.room-card-img {
    height: 200px;
    object-fit: cover;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    border-bottom: 3px solid var(--gold-dark);
}
.room-card-body {
    padding: 1.5rem;
}
</style>

<section class="hero-section">
    <div class="container">
        <h1 class="text-gold">Bienvenue à Hôtelsys</h1>
        <p>Confort, élégance et service premium. Réservez votre séjour inoubliable.</p>
        <a class="btn btn-gold" href="#chambres-disponibles">Réserver maintenant</a>
    </div>
</section>

<div class="container my-5">

    <div class="row">

        <div class="col-lg-7 mb-5">
            <div class="card h-100">
                <h2 class="text-gold mb-4">L'Expérience Hôtelsys</h2>
                <p class="lead" style="color: var(--text-dark);">Hôtelsys est synonyme de raffinement absolu. Nous nous engageons à offrir une évasion où chaque détail est pensé pour votre bien-être. Profitez de nos services exclusifs : spa, gastronomie, conciergerie 24/7 et navette aéroport privée.</p>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <h6 class="text-gold">Service 24/7</h6>
                        <p>Conciergerie et room service dédiés.</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-gold">Restauration Fine</h6>
                        <p>Cuisine créative et cartes des vins prestigieuses.</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-gold">Espace Bien-être</h6>
                        <p>Accès au Spa, hammam, et piscine intérieure.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-5">
            <div class="card h-100" id="espace-client">
                <h4 class="mb-4 text-gold">Espace Client</h4>
                <?= $client_login_message ?>
                <?php if (!empty($_SESSION['client_id'])): ?>
                    <div class="alert alert-success text-center">
                        Bonjour <?= htmlspecialchars($_SESSION['client_nom'] ?? 'Client'); ?>, vous êtes connecté.
                    </div>
                    <!-- AJOUT DU BOUTON DASHBOARD CLIENT -->
                    <a href="client_dashboard.php" class="btn btn-gold w-100 mb-3">Mon Tableau de Bord</a>
                    <a href="reservations/ajouter.php" class="btn btn-gold w-100 mb-3">Créer une nouvelle réservation</a>
                    <a href="client_logout.php" class="btn btn-outline-light w-100">Déconnexion</a>
                <?php else: ?>
                    <form action="client_login.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="client_email" class="form-control" placeholder="Email ou Nom d'utilisateur" required>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="client_password" class="form-control" placeholder="Mot de passe" required>
                        </div>
                        <button class="btn btn-gold w-100 mb-3">Se connecter</button>
                    </form>
                    <a href="client_register.php" class="btn btn-outline-light w-100">Créer un compte client</a>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="row my-5">
        <div class="col-12">
            <div class="card p-4 text-center">
                <h2 class="text-gold mb-4">Ce que nos Clients Disent</h2>
                <blockquote class="blockquote mb-4" style="border-left: 5px solid var(--gold); padding-left: 20px;">
                    <p class="mb-0 lead fst-italic">"L'expérience Hôtelsys est incomparable. Un service d'une attention rare et un cadre à couper le souffle. J'y reviendrai sans hésiter."</p>
                    <footer class="blockquote-footer mt-2" style="color: var(--gold-dark); font-size: 1.1rem;">
                        Mme DUPONT (Paris, France)
                    </footer>
                </blockquote>
            </div>
        </div>
    </div>

    <hr>

    <h2 class="text-gold text-center mt-5 mb-4" id="chambres-disponibles">Nos Chambres de Prestige</h2>
    <div class="row">
    <?php
    $images = [
        PROJECT_ROOT . '/assets/luxe.png',
        PROJECT_ROOT . '/assets/simple.png',
        PROJECT_ROOT . '/assets/suite.png'
    ];
    $image_counter = 0;

    if ($result_chambres && pg_num_rows($result_chambres) > 0):
        while ($row = pg_fetch_assoc($result_chambres)):
            $desc = trim($row['description']);
            if ($desc === '') $desc = 'Confort moderne et services inclus pour un séjour inoubliable.';

            $current_image = $images[$image_counter % count($images)];
            $image_counter++;
    ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card p-0">
                <img src="<?= $current_image; ?>" class="card-img-top room-card-img" alt="image de chambre <?= htmlspecialchars($row['type_nom']); ?>">
                <div class="room-card-body">
                    <h4 class="text-gold mb-1"><?= htmlspecialchars($row['type_nom']); ?></h4>
                    <p class="small">Étage <?= htmlspecialchars($row['etage']); ?> • Max. <?= htmlspecialchars($row['capacite_max']); ?> personnes</p>
                    <p><?= htmlspecialchars(mb_strimwidth($desc, 0, 80, '...')); ?></p>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                       <span class="room-price"><?= number_format($row['prix_base'], 2, ',', ' '); ?> €</span>
    
                       <a href="reserver_chambre.php?id=<?= (int)$row['id_chambre']; ?>" class="btn btn-gold btn-sm">Réserver</a>
</div>
                </div>
            </div>
        </div>
    <?php
        endwhile;
    else:
    ?>
        <div class="col-12">
            <div class="alert alert-warning text-center mt-4">Toutes nos chambres de luxe sont actuellement réservées. Veuillez revenir plus tard ou nous contacter directement.</div>
        </div>
    <?php endif; ?>
    </div>

    <div class="row my-5">
        <div class="col-12">
            <div class="card p-4 text-center">
                <h3 class="text-gold mb-3">Nous Contacter</h3>
                <p>Pour toute demande spéciale ou assistance, notre équipe est à votre disposition.</p>
                <div class="row justify-content-center mt-3">
                    <div class="col-md-4">
                        <h6 class="text-gold">Adresse</h6>
                        <p>123 Rue de l'Élégance, 75008 Paris, France</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-gold">Téléphone</h6>
                        <p>+33 1 23 45 67 89</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-gold">Email</h6>
                        <p>contact@hotelsys.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/public_footer.php';
?>