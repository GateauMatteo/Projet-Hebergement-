<?php
    include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Héberge 03</title>
    <!-- Bootstrap CSS local -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="<?php echo isset($_SESSION['mail']) ? 'logged-in' : ''; ?>">

    <!-- Section Hero -->
    <section id="hero" class="text-center bg-light py-5">
        <div class="container">
            <h1 class="display-4 fw-bold text-primary">Bienvenue sur Héberge 03</h1>
            <p class="lead">Découvrez nos solutions d'hébergement innovantes et adaptées à vos projets web.</p>
            <a href="#services" class="btn btn-primary btn-lg shadow">En savoir plus</a>
        </div>
    </section>

    <!-- Présentation des Services -->
    <section id="services" class="container my-5">
        <h2 class="text-center mb-4">Nos Services</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h3 class="card-title text-primary">Serveur web partagé</h3>
                        <p class="card-text">Idéal pour héberger plusieurs sites en toute simplicité.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h3 class="card-title text-primary">Serveur dédié</h3>
                        <p class="card-text">Une solution personnalisée pour des performances optimales.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h3 class="card-title text-primary">Cloud Hosting</h3>
                        <p class="card-text">Une infrastructure flexible et évolutive pour vos projets.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Témoignages -->
    <section id="testimonials" class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Avis Clients</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <blockquote class="blockquote text-center">
                                <p>"Service impeccable, merci beaucoup !"</p>
                                <footer class="blockquote-footer">Client 1</footer>
                            </blockquote>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <blockquote class="blockquote text-center">
                                <p>"L'équipe est très réactive et compétente."</p>
                                <footer class="blockquote-footer">Client 2</footer>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- À propos -->
    <section id="about" class="container my-5">
        <h2 class="text-center mb-4">Qui sommes-nous ?</h2>
        <p class="text-center fs-5">
            Nous sommes une entreprise spécialisée dans l'hébergement web, offrant des solutions adaptées à tous les besoins. 
            Profitez d'une infrastructure moderne et d'un support client dédié.
        </p>
    </section>

   

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS local -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
