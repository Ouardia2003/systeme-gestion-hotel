 </div> <footer class="luxury-footer mt-5 pt-5 pb-3">
    <div class="container">
        <div class="row">
            
            <div class="col-md-4 mb-4">
                <h5 class="footer-title text-gold mb-3">Hôtelsys</h5>
                <ul class="list-unstyled footer-links">
                    <li><p>123 Rue de l'Élégance</p></li>
                    <li><p>75008 Paris, France</p></li>
                    <li><a href="mailto:contact@hotelsys.com">contact@hotelsys.com</a></li>
                    <li><a href="tel:+33123456789">+33 6 23 45 67 88</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-4">
                <h5 class="footer-title text-gold mb-3">Navigation</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= PROJECT_ROOT ?>/index.php#chambres-disponibles">Réserver une Chambre</a></li>
                    <li><a href="<?= PROJECT_ROOT ?>/login.php">Espace Employé</a></li>
                    <li><a href="<?= PROJECT_ROOT ?>/client_register.php">Créer un Compte Client</a></li>
                </ul>
            </div>

            <div class="col-md-4 mb-4">
                <h5 class="footer-title text-gold mb-3">Équipe Projet</h5>
                <ul class="list-unstyled footer-team">
                    <li>NOUHA Elyamany</li>
                    <li>Lisa Issad</li>
                    <li>Ouardia Achab</li>
                </ul>
            </div>
            
        </div>
        
        <hr class="footer-separator">
        
    
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>

.luxury-footer {
    background-color: var(--black); 
    color: var(--white); 
    border-top: 3px solid var(--gold); 
    font-family: var(--font-secondary);
}

.footer-title {
    font-family: var(--font-primary);
    font-size: 1.5rem;
    color: var(--gold) !important;
}

.footer-links a, .footer-team {
    color: var(--white);
    text-decoration: none;
    transition: color var(--transition-speed) ease;
    padding: 0.2rem 0;
}

.footer-links a:hover {
    color: var(--gold); 
    text-decoration: underline;
}

.footer-team li {
    font-size: 1.1rem;
    color: var(--gold); 
}

.footer-separator {
    background-color: rgba(255, 255, 255, 0.1);
    border: none;
    height: 1px;
}
</style>