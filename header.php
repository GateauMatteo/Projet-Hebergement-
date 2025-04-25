<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <!-- Bouton hamburger toujours visible -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" 
                    aria-controls="offcanvasNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Logo centré -->
            <a class="navbar-brand mx-auto fw-bold" href="index.php">Heberge03</a>

            <!-- Menu Offcanvas avec fond blanc -->
            <div class="offcanvas offcanvas-start bg-white" tabindex="-1" id="offcanvasNavbar" 
                 aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav">
                        <!-- Lien Accueil -->
                        <li class="nav-item">
                            <a style="color:black;" class="nav-link" href="index.php">Accueil</a>
                        </li>
                        <!-- Liens dynamiques selon l'état de la session -->
                        <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
                            <li class="nav-item">
                                <a style="color:black;" class="nav-link" href="profile.php">Profil</a>
                            </li>
                            <li class="nav-item">
                                <a style="color:black;" class="nav-link" href="logout.php">Déconnexion</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a style="color:black;" class="nav-link" href="login.php">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a style="color:black;" class="nav-link" href="signup.php">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
